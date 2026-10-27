<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * Redirects already-authenticated users away from the login page.
     * Uses smart permission-aware redirect so users without Dashboard
     * permission land on their first allowed module instead of getting 403.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Distributor users go directly to their dedicated portal
                if (
                    ($user->usertype ?? '') === 'distributor' ||
                    (($user->distributor ?? null) && !$user->hasRole('Super Admin') && $user->email !== 'admin@admin.com')
                ) {
                    return redirect()->route('distributor.dashboard');
                }

                // Super Admin / users with Dashboard permission → /home
                $canDashboard = $user->email === 'admin@admin.com'
                    || $user->hasRole(['Super Admin', 'super-admin'])
                    || $user->can('Dashboard');

                if ($canDashboard) {
                    return redirect(RouteServiceProvider::HOME);
                }

                // Smart fallback: redirect to the first module the user CAN access
                $fallbacks = [
                    'Sales'             => 'sale.screen',
                    'Purchase'          => 'Purchase.home',
                    'Products'          => 'product',
                    'Customer'          => 'customers.index',
                    'Vendor'            => 'vendors',
                    'Production'        => 'production.index',
                    'Raw Materials'     => 'raw-materials.index',
                    'Stock Transfer'    => 'stock_transfers.index',
                    'Item Stock Report' => 'report.item_stock',
                    'Sale Report'       => 'report.sale',
                    'Purchase Report'   => 'report.purchase',
                    'Customer Ledger'   => 'report.customer.ledger',
                    'Vendor Ledger'     => 'report.vendor.ledger',
                    'Char Of Accounts'  => 'view_all',
                    'Payment Voucher'   => 'all-Payment-vochers',
                    'Receipts Voucher'  => 'all-recepit-vochers',
                    'Expense Voucher'   => 'all-expense-vochers',
                    'Cashbook'          => 'cashbook',
                    'Category'          => 'Category.home',
                    'Roles'             => 'roles.index',
                    'Users'             => 'users.index',
                    'Settings'          => 'settings.index',
                ];

                foreach ($fallbacks as $permission => $routeName) {
                    if ($user->can($permission) && Route::has($routeName)) {
                        return redirect()->route($routeName);
                    }
                }

                // Absolute last resort → HomeController will handle 403
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
