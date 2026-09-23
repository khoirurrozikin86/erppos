<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;

use App\Domain\Categories\Queries\CategoryTableQuery;
use App\Domain\Categories\Services\CategoryService;

use App\Models\Category;

use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('super.categories.index');
    }

    public function dt(CategoryTableQuery $q)
    {
        return DataTables::eloquent($q->builder())

            // =========================
            // STATUS
            // =========================
            ->addColumn('status', function (Category $category) {
                return $category->is_active
                    ? 'Active'
                    : 'Not Active';
            })

            // =========================
            // ACTIONS
            // =========================
            ->addColumn('actions', function (Category $category) {

                $actions = [

                    // EDIT
                    [
                        'type' => 'edit',

                        'label' => 'Edit',

                        'icon' => 'edit-2',

                        'update_url' => route(
                            'super.categories.update',
                            $category->getRouteKey()
                        ),

                        'payload' => [
                            'id' => $category->id,
                            'code' => $category->code,
                            'name' => $category->name,
                            'description' => $category->description,
                            'is_active' => $category->is_active,
                        ],
                    ],

                    // DELETE
                    [
                        'type' => 'delete',

                        'url' => route(
                            'super.categories.destroy',
                            $category->getRouteKey()
                        ),

                        'label' => 'Delete',

                        'icon' => 'trash-2',

                        'confirm' =>
                        "Hapus kategori {$category->name}?",

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

    // =========================
    // STORE
    // =========================

    public function store(
        CategoryStoreRequest $request,
        CategoryService $service
    ) {
        $category = $service->create(
            $request->validated()
        );

        return $request->ajax() || $request->expectsJson()
            ? response()->json([
                'message' => 'Category created',
                'id' => $category->id,
            ], 201)

            : back()->with(
                'success',
                'Category created'
            );
    }

    // =========================
    // UPDATE
    // =========================

    public function update(
        CategoryUpdateRequest $request,
        Category $category,
        CategoryService $service
    ) {
        $service->update(
            $category,
            $request->validated()
        );

        return $request->ajax() || $request->expectsJson()
            ? response()->json([
                'message' => 'Category updated',
            ])

            : back()->with(
                'success',
                'Category updated'
            );
    }

    // =========================
    // DELETE
    // =========================

    public function destroy(
        Category $category,
        CategoryService $service
    ) {
        // Jangan hapus kategori yang sudah digunakan product
        if ($category->products()->exists()) {

            return response()->json([
                'message' =>
                'Category cannot be deleted because it is already used by a product.',
            ], 422);
        }

        $service->delete($category);

        return request()->ajax() || request()->expectsJson()
            ? response()->json([
                'message' => 'Category deleted',
            ])

            : redirect()
            ->route('super.categories.index')
            ->with(
                'success',
                'Category deleted'
            );
    }
}
