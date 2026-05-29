<?php

namespace App\Http\Controllers\MasterFile;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterFile\ItemStoreRequest;
use App\Http\Requests\MasterFile\ItemUpdateRequest;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $items = Item::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('item_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('master-file/Item', [
            'items' => $items,
            'filters' => [
                'search' => $search,
            ],
            'nextItemCode' => $this->nextItemCode(),
            'pageSubtitle' => 'Kelola data item',
        ]);
    }

    public function store(ItemStoreRequest $request): RedirectResponse
    {
        Item::create([
            'item_code' => $this->nextItemCode(),
            'description' => $request->string('description')->trim()->toString(),
            'price' => $this->normalizePrice($request->input('price')),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Item created.'),
        ]);

        return back();
    }

    public function update(ItemUpdateRequest $request, Item $item): RedirectResponse
    {
        $item->update([
            'description' => $request->string('description')->trim()->toString(),
            'price' => $this->normalizePrice($request->input('price')),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Item updated.'),
        ]);

        return back();
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Item deleted.'),
        ]);

        return back();
    }

    private function nextItemCode(): string
    {
        $latestNumber = Item::query()
            ->pluck('item_code')
            ->map(function (string $code): int {
                if (! preg_match('/^ITEM-(\d+)$/', $code, $matches)) {
                    return 0;
                }

                return (int) $matches[1];
            })
            ->max();

        if (! is_int($latestNumber) || $latestNumber < 1) {
            return 'ITEM-0001';
        }

        return sprintf('ITEM-%04d', $latestNumber + 1);
    }

    private function normalizePrice(mixed $value): string
    {
        if (is_numeric($value)) {
            return (string) $value;
        }

        if (! is_string($value)) {
            return '0';
        }

        $digits = preg_replace('/[^\d]/', '', $value) ?? '0';

        return $digits === '' ? '0' : $digits;
    }
}
