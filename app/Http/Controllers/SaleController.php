<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SaleController extends Controller
{
    public function __construct(private SaleService $sales)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:' . Sale::STATUS_COMPLETED . ',' . Sale::STATUS_CANCELLED],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ], [
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $sales = Sale::with('user')
            ->visibleTo($request->user())
            ->filter($filters)
            ->latest('sold_at')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        // Hanya produk yang masih punya stok yang ditawarkan di form.
        $productOptions = Product::where('stock', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'label' => "{$p->sku} - {$p->name} (stok {$p->stock})",
                'price' => (float) $p->selling_price,
            ])
            ->values();

        return view('sales.create', compact('productOptions'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        // Hanya product_id & quantity yang dipakai dari input. Harga & total dihitung di service.
        $sale = $this->sales->create(
            $request->user(),
            $request->validated('items'),
            $request->validated('customer_name'),
        );

        return redirect()->route('sales.show', $sale)->with('success', "Penjualan {$sale->sale_number} berhasil disimpan.");
    }

    public function show(Sale $sale)
    {
        Gate::authorize('view-transaction', $sale);

        $sale->load(['user', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        $this->sales->cancel($sale);

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan dibatalkan dan stok dikembalikan.');
    }
}
