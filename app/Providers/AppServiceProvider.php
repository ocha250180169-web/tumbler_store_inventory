<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tampilan pagination memakai Bootstrap 5 (sesuai layout)
        Paginator::useBootstrapFive();

        // ===== Authorization (dicek di SERVER, bukan hanya menyembunyikan tombol) =====
        Gate::define('manage-master', fn (User $u) => $u->isAdmin());        // kategori, produk (CUD), pemasok
        Gate::define('manage-users', fn (User $u) => $u->isAdmin());         // user & role
        Gate::define('cancel-transaction', fn (User $u) => $u->isAdmin());   // pembatalan transaksi
        Gate::define('manage-transactions', fn (User $u) => in_array(
            $u->role,
            [User::ROLE_ADMIN, User::ROLE_STAFF],
            true
        ));
        // Admin boleh melihat semua transaksi, staff hanya transaksi miliknya.
        Gate::define('view-transaction', fn (User $u, $transaction) => $u->isAdmin() || $transaction->user_id === $u->id);

        // Direktif @rupiah($angka) untuk format mata uang di Blade
        Blade::directive('rupiah', fn (string $expression) => "<?php echo 'Rp ' . number_format((float) ({$expression}), 0, ',', '.'); ?>");
    }
}
