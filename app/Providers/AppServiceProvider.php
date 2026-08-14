<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'components.quick-transaction-modal'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $globalAccounts = Account::where('user_id', $user->id)->where('is_active', true)->orderBy('name')->get();
                $globalCategories = Category::forUser($user->id)->orderBy('name')->get();

                $view->with([
                    'globalAccounts' => $globalAccounts,
                    'globalCategories' => $globalCategories,
                ]);
            }
        });
    }
}
