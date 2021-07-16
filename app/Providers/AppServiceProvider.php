<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
                Log::channel('sql')->info([
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //$this->createSaraminDriver();
    }

    private function createSaraminDriver()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'saramin',
            function ($app) use ($socialite) {
                $config = $app['config']['services.saramin'];
                return $socialite->buildProvider(SaraminProvider::class, $config);
            }
        );
    }
}
