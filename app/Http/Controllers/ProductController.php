<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        // with('category') = eager loading, menghindari masalah N+1 query.
        $products = Product::with('category')
            ->search($filters['q'] ?? null)
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString(); // filter tetap terbawa saat pindah halaman

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function lowStock()
    {
        $products = Product::with('category')
            ->lowStock()
            ->orderBy('stock')
            ->paginate(10);

        return view('products.low-stock', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load('category');

        $saleItems = $product->saleItems()->with('sale')->latest()->limit(5)->get();
        $purchaseItems = $product->purchaseItems()->with('purchase')->latest()->limit(5)->get();

        return view('products.show', compact('product', 'saleItems', 'purchaseItems'));
    }

    public function create()
    {
        return view('products.create', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        // Stok awal selalu 0. Stok bertambah lewat transaksi pembelian.
        $product = Product::create($request->validated());

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil ditambahkan. Tambahkan stok lewat menu Pembelian.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        // Field stok tidak ada di validated() / fillable, jadi tidak bisa diubah lewat form ini.
        $product->update($request->validated());

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Produk yang sudah dipakai transaksi tidak boleh dihapus (menjaga riwayat).
        if ($product->isUsedInTransactions()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah dipakai dalam transaksi.');
        }

        $product->delete(); // soft delete

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
