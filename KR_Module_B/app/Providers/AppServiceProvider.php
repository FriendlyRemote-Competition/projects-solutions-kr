<?php

namespace App\Providers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        Model::unguard();

        Carbon::serializeUsing(fn($date) => $date->toIso8601String());

        Auth::viaRequest('admin-token', function (Request $request) {
            $token = $request->bearerToken();
            return $token ? User::where('api_token', $token)->first() : null;
        });

        Gate::define('admin', function(User $user) {
            return $user->role === 'admin';
        });
    }
}
