<?php

namespace App\Providers;

use App\State\CarResponseStateProcessor;
use ApiPlatform\State\ProcessorInterface;
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
		$this->app->tag(CarResponseStateProcessor::class, ProcessorInterface::class);
    }
}
