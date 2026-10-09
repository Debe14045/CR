<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();

        View::composer(['components.top-navbar', 'components.sidebar', 'layouts.app', 'notifications.*', 'cr.index'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $unreadCount = Notification::forUser($user)->unread()->count();
                $recentNotifs = Notification::forUser($user)->with('changeRequest')->latest()->limit(5)->get();
                $view->with([
                    'unreadNotificationsCount' => $unreadCount,
                    'recentNotifications' => $recentNotifs,
                ]);
            } else {
                $view->with([
                    'unreadNotificationsCount' => 0,
                    'recentNotifications' => collect(),
                ]);
            }
        });
    }
}
