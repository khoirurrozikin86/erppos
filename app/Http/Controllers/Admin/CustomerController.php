<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Customers\Queries\CustomerTableQuery;
use App\Domain\Customers\Services\CustomerService;
use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerStoreRequest;
use App\Http\Requests\Admin\CustomerUpdateRequest;
use App\Models\Customer;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        return view('super.customers.index');
    }

    public function dt(CustomerTableQuery $q)
    {
        return DataTables::eloquent($q->builder())

            ->addColumn('status', function (Customer $customer) {
                return $customer->is_active
                    ? 'Active'
                    : 'Not Active';
            })

            ->addColumn('actions', function (Customer $customer) {

                $actions = [
                    [
                        'type' => 'edit',
                        'label' => 'Edit',
                        'icon' => 'edit-2',

                        'update_url' => route(
                            'super.customers.update',
                            $customer->getRouteKey()
                        ),

                        'payload' => [
                            'id' => $customer->id,
                            'code' => $customer->code,
                            'name' => $customer->name,
                            'customer_type' => $customer->customer_type,
                            'contact_person' => $customer->contact_person,
                            'phone' => $customer->phone,
                            'email' => $customer->email,
                            'website' => $customer->website,
                            'tax_number' => $customer->tax_number,
                            'address' => $customer->address,
                            'city' => $customer->city,
                            'province' => $customer->province,
                            'postal_code' => $customer->postal_code,
                            'payment_term' => $customer->payment_term,
                            'credit_limit' => $customer->credit_limit,
                            'notes' => $customer->notes,
                            'is_active' => $customer->is_active,
                        ],
                    ],

                    [
                        'type' => 'delete',
                        'url' => route(
                            'super.customers.destroy',
                            $customer->getRouteKey()
                        ),
                        'label' => 'Delete',
                        'icon' => 'trash-2',
                        'confirm' =>
                        "Hapus customer {$customer->name}?",
                        'disabled' => false,
                    ],
                ];

                return view(
                    'admin.partials.table-actions',
                    compact('actions')
                )->render();
            })

            ->rawColumns([
                'status',
                'actions',
            ])

            ->toJson();
    }

    public function export()
    {
        return Excel::download(
            new CustomersExport,
            'customers-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function store(
        CustomerStoreRequest $request,
        CustomerService $service
    ) {
        $customer = $service->create(
            $request->validated()
        );

        return $request->ajax() || $request->expectsJson()
            ? response()->json([
                'message' => 'Customer created',
                'id' => $customer->id,
            ], 201)
            : back()->with(
                'success',
                'Customer created'
            );
    }

    public function update(
        CustomerUpdateRequest $request,
        Customer $customer,
        CustomerService $service
    ) {
        $service->update(
            $customer,
            $request->validated()
        );

        return $request->ajax() || $request->expectsJson()
            ? response()->json([
                'message' => 'Customer updated',
            ])
            : back()->with(
                'success',
                'Customer updated'
            );
    }

    public function destroy(
        Customer $customer,
        CustomerService $service
    ) {
        $service->delete($customer);

        return request()->ajax() || request()->expectsJson()
            ? response()->json([
                'message' => 'Customer deleted',
            ])
            : redirect()
            ->route('super.customers.index')
            ->with(
                'success',
                'Customer deleted'
            );
    }
}
