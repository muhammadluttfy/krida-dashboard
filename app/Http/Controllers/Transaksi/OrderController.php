<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\OrderStoreRequest;
use App\Http\Requests\Transaksi\OrderUpdateRequest;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $orders = Order::query()
            ->with([
                'customer:id,customer_code,full_name',
                'orderItems.item:id,item_code,description,price',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                            $customerQuery->where('full_name', 'like', "%{$search}%")
                                ->orWhere('customer_code', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('transaksi/Order', [
            'orders' => $orders,
            'customers' => Customer::query()
                ->orderBy('full_name')
                ->get(['id', 'customer_code', 'full_name']),
            'items' => Item::query()
                ->orderBy('description')
                ->get(['id', 'item_code', 'description', 'price']),
            'filters' => [
                'search' => $search,
            ],
            'nextOrderNumber' => $this->nextOrderNumber(),
            'pageSubtitle' => 'Kelola data transaksi order',
        ]);
    }

    public function searchItems(Request $request)
    {
        $search = $request->string('search')->trim()->toString();

        $items = Item::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('item_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('description')
            ->limit(20)
            ->get(['id', 'item_code', 'description', 'price']);

        return response()->json($items);
    }

    public function store(OrderStoreRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $data = $request->validated();
            $calculation = $this->calculateOrderTotals($data['items'] ?? []);

            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'order_date' => $request->date('order_date')->toDateString(),
                'customer_id' => (int) $data['customer_id'],
                ...$calculation['summary'],
            ]);

            $order->orderItems()->createMany($calculation['items']);
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Order created.'),
        ]);

        return back();
    }

    public function update(OrderUpdateRequest $request, Order $order): RedirectResponse
    {
        DB::transaction(function () use ($request, $order): void {
            $data = $request->validated();
            $calculation = $this->calculateOrderTotals($data['items'] ?? []);

            $order->update([
                'order_date' => $request->date('order_date')->toDateString(),
                'customer_id' => (int) $data['customer_id'],
                ...$calculation['summary'],
            ]);

            $order->orderItems()->delete();
            $order->orderItems()->createMany($calculation['items']);
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Order updated.'),
        ]);

        return back();
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Order deleted.'),
        ]);

        return back();
    }

    private function nextOrderNumber(): string
    {
        $year = now('Asia/Jakarta')->format('Y');

        $latestNumber = Order::query()
            ->where('order_number', 'like', "SO-{$year}-%")
            ->pluck('order_number')
            ->map(function (string $code) use ($year): int {
                if (! preg_match("/^SO-{$year}-(\\d+)$/", $code, $matches)) {
                    return 0;
                }

                return (int) $matches[1];
            })
            ->max();

        if (! is_int($latestNumber) || $latestNumber < 1) {
            return sprintf('SO-%s-%05d', $year, 1);
        }

        return sprintf('SO-%s-%05d', $year, $latestNumber + 1);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{summary: array<string, string>, items: array<int, array<string, mixed>>}
     */
    private function calculateOrderTotals(array $items): array
    {
        $itemIds = collect($items)
            ->pluck('item_id')
            ->map(fn ($value): int => (int) $value)
            ->filter(fn (int $value): bool => $value > 0)
            ->values();

        $masterItems = Item::query()
            ->whereIn('id', $itemIds)
            ->get(['id', 'price'])
            ->keyBy('id');

        $summary = [
            'subtotal' => '0',
            'discount_amount' => '0',
            'net_amount' => '0',
            'dpp_amount' => '0',
            'ppn_amount' => '0',
            'grand_total' => '0',
        ];

        $calculatedItems = [];
        $subtotal = 0;
        $discountAmount = 0;

        foreach ($items as $row) {
            $itemId = (int) ($row['item_id'] ?? 0);
            $masterItem = $masterItems->get($itemId);

            if (! $masterItem) {
                continue;
            }

            $quantity = max(1, (int) ($row['quantity'] ?? 1));
            $unitPrice = $this->normalizeMoney($row['unit_price'] ?? $masterItem->price);
            $discountPercent = $this->normalizePercent($row['discount_percent'] ?? 0);
            $description = trim((string) ($row['description'] ?? ''));
            $grossAmount = (int) round($unitPrice * $quantity);
            $rowDiscountAmount = (int) round($grossAmount * ($discountPercent / 100));
            $lineTotal = max(0, $grossAmount - $rowDiscountAmount);

            $subtotal += $grossAmount;
            $discountAmount += $rowDiscountAmount;

            $calculatedItems[] = [
                'item_id' => $itemId,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => (string) $unitPrice,
                'discount_amount' => (string) $rowDiscountAmount,
                'line_total' => (string) $lineTotal,
            ];
        }

        $netAmount = max(0, $subtotal - $discountAmount);
        $dppAmount = $netAmount;
        $ppnAmount = (int) round($dppAmount * 0.11);
        $grandTotal = $dppAmount + $ppnAmount;

        $summary['subtotal'] = (string) $subtotal;
        $summary['discount_amount'] = (string) $discountAmount;
        $summary['net_amount'] = (string) $netAmount;
        $summary['dpp_amount'] = (string) $dppAmount;
        $summary['ppn_amount'] = (string) $ppnAmount;
        $summary['grand_total'] = (string) $grandTotal;

        return [
            'summary' => $summary,
            'items' => $calculatedItems,
        ];
    }

    private function normalizeMoney(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) round($value);
        }

        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        if (! is_string($value)) {
            return 0;
        }

        $digits = preg_replace('/[^\d]/', '', $value) ?? '';

        return $digits === '' ? 0 : (int) $digits;
    }

    private function normalizePercent(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return max(0, (float) $value);
        }

        if (is_numeric($value)) {
            return max(0, (float) $value);
        }

        if (! is_string($value)) {
            return 0.0;
        }

        $normalized = str_replace(',', '.', trim($value));

        return is_numeric($normalized) ? max(0, (float) $normalized) : 0.0;
    }
}
