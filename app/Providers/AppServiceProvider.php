<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        // Share vendor unread count with all vendor views
        View::composer('vendor.partials.nav', function ($view) {
            if (Auth::check()) {
                $vendorUnread = \App\Models\Conversation::where(function($q){
                    $q->where('buyer_id', Auth::id())->orWhere('seller_id', Auth::id());
                })->whereHas('messages', function($q){
                    $q->where('is_read', false)->where('sender_id', '!=', Auth::id());
                })->count();
                $view->with('vendorUnread', $vendorUnread);
            }
        });
    }
}
