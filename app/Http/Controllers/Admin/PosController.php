<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Pos\Queries\PosSessionQuery;
use App\Domain\Pos\Services\PosSaleService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\PosSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request, PosSessionQuery $query): View
    {
        return view('super.pos.index', [
            'session' => $query->activeSession((int) $request->user()->id),
            'paymentAccounts' => $query->activePaymentAccounts(),
            'categories' => Category::query()->where('is_active', true)
                ->whereHas('products', fn ($products) => $products->where('is_active', true)->where('allow_sales', true))
                ->withCount(['products' => fn ($products) => $products->where('is_active', true)->where('allow_sales', true)])
                ->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);
        $term = trim($data['q'] ?? '');
        $products = Product::query()->where('is_active', true)->where('allow_sales', true)
            ->when($term !== '', fn ($query) => $query->where(fn ($search) => $search->where('code', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%")->orWhere('barcode', 'like', "%{$term}%")))
            ->when(!empty($data['category_id']), fn ($query) => $query->where('category_id', $data['category_id']))
            ->with(['unit:id,name,symbol', 'stock:id,product_id,quantity', 'primaryImage:id,product_id,path'])
            ->orderBy('name')->limit($term === '' ? 100 : 50)->get(['id', 'code', 'barcode', 'name', 'category_id', 'unit_id', 'sales_price', 'taxable', 'tax_rate', 'allow_discount', 'track_stock'])
            ->map(fn (Product $product) => [
                'id' => $product->id, 'code' => $product->code, 'name' => $product->name,
                'category_id' => $product->category_id,
                'image' => $product->primaryImage ? asset('storage/' . $product->primaryImage->path) : null,
                'unit' => $product->unit?->symbol ?: $product->unit?->name ?: '—',
                'price' => (float) $product->sales_price, 'taxable' => $product->taxable,
                'tax_rate' => (float) $product->tax_rate, 'allow_discount' => $product->allow_discount,
                'track_stock' => $product->track_stock, 'stock' => (float) ($product->stock?->quantity ?? 0),
            ]);
        return response()->json(['products' => $products]);
    }

    public function checkout(Request $request, PosSaleService $service): JsonResponse
    {
        $data = $request->validate([
            'pos_session_id' => ['required', 'integer', 'exists:pos_sessions,id'],
            'cash_bank_account_id' => ['required', 'integer', 'exists:cash_bank_accounts,id'],
            'payment_method' => ['required', 'in:cash,bank_transfer,card'],
            'paid_amount' => ['required_if:payment_method,cash', 'nullable', 'numeric', 'gt:0', 'decimal:0,2'],
            'discount_rate' => ['required', 'numeric', 'min:0', 'max:100', 'decimal:0,2'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'decimal:0,4'],
        ]);
        $sale = $service->create($data, (int) $request->user()->id);
        return response()->json([
            'message' => "Penjualan {$sale->number} berhasil disimpan.", 'number' => $sale->number,
            'subtotal' => (float) $sale->subtotal, 'discount' => (float) $sale->discount_amount,
            'tax' => (float) $sale->tax_amount, 'total' => (float) $sale->total_amount,
            'change' => (float) $sale->change_amount,
            'void_url' => route('super.pos.void', $sale),
        ], 201);
    }

    public function void(Request $request, PosSale $posSale, PosSaleService $service): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);
        $sale = $service->void(
            $posSale,
            $data['reason'],
            (int) $request->user()->id,
            $request->user()->can('pos.void-closed-session'),
        );

        return response()->json([
            'message' => "Transaksi {$sale->number} berhasil di-void. Stok dipulihkan dan refund tercatat.",
            'number' => $sale->number,
            'status' => $sale->status,
        ]);
    }
}
