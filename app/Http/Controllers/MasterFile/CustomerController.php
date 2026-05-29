<?php

namespace App\Http\Controllers\MasterFile;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterFile\CustomerStoreRequest;
use App\Http\Requests\MasterFile\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $customers = Customer::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('master-file/Customer', [
            'customers' => $customers,
            'filters' => [
                'search' => $search,
            ],
            'nextCustomerCode' => $this->nextCustomerCode(),
            'pageSubtitle' => 'Kelola data customer',
        ]);
    }

    public function store(CustomerStoreRequest $request): RedirectResponse
    {
        Customer::create([
            'customer_code' => $this->nextCustomerCode(),
            'full_name' => $request->string('full_name')->trim()->toString(),
            'address' => $this->normalizeNullableField($request->input('address')),
            'phone_number' => $this->normalizePhoneNumber($request->input('phone_number')),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Customer created.'),
        ]);

        return back();
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update([
            'full_name' => $request->string('full_name')->trim()->toString(),
            'address' => $this->normalizeNullableField($request->input('address')),
            'phone_number' => $this->normalizePhoneNumber($request->input('phone_number')),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Customer updated.'),
        ]);

        return back();
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Customer deleted.'),
        ]);

        return back();
    }

    private function nextCustomerCode(): string
    {
        $latestNumber = Customer::query()
            ->pluck('customer_code')
            ->map(function (string $code): int {
                if (! preg_match('/^CUST-(\d+)$/', $code, $matches)) {
                    return 0;
                }

                return (int) $matches[1];
            })
            ->max();

        if (! is_int($latestNumber) || $latestNumber < 1) {
            return 'CUST-0001';
        }

        return sprintf('CUST-%04d', $latestNumber + 1);
    }

    private function normalizeNullableField(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizePhoneNumber(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        return $digits === '' ? null : $digits;
    }
}
