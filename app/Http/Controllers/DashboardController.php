<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Semua angka dihitung dengan query agregat ke database (bukan data statis).
        $sales = fn () => Sale::completed()->visibleTo($user);

        $stats = [
            'total_products' => Product::count(),
            'low_stock_count' => Product::lowStock()->count(),
            'sales_today' => (float) $sales()->whereDate('sold_at', today())->sum('total'),
            'sales_month' => (float) $sales()
                ->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total'),
            'sales_count' => $sales()->count(),
            'purchase_count' => Purchase::confirmed()->visibleTo($user)->count(),
        ];

        $lowStockProducts = Product::with('category')
            ->lowStock()
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $latestSales = Sale::with('user')
            ->visibleTo($user)
            ->latest('sold_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'lowStockProducts', 'latestSales'));
    }
}
