<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

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
        View::composer('*', function ($view) {
            $user = Auth::user();

            $notifications = collect();

            if ($user) {
                $notifications = Notification::where('id_user', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
            }

            $view->with('notifications', $notifications);
        });
    }
}
