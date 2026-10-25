<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $purchases)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:' . Purchase::STATUS_CONFIRMED . ',' . Purchase::STATUS_CANCELLED],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ], [
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $purchases = Purchase::with(['supplier', 'user'])
            ->visibleTo($request->user())
            ->filter($filters)
            ->latest('purchased_at')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();

        $productOptions = Product::orderBy('name')
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'label' => "{$p->sku} - {$p->name} (stok {$p->stock})",
                'cost' => (float) $p->purchase_price,
            ])
            ->values();

        return view('purchases.create', compact('suppliers', 'productOptions'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $purchase = $this->purchases->create(
            $request->user(),
            (int) $request->validated('supplier_id'),
            $request->validated('items'),
        );

        return redirect()->route('purchases.show', $purchase)->with('success', "Pembelian {$purchase->purchase_number} berhasil disimpan dan stok bertambah.");
    }

    public function show(Purchase $purchase)
    {
        Gate::authorize('view-transaction', $purchase);

        $purchase->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase): RedirectResponse
    {
        $this->purchases->cancel($purchase);

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian dibatalkan dan stok dikurangi kembali.');
    }
}
