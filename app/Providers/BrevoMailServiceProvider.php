<?php

namespace App\Providers;

use App\Mail\BrevoTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class BrevoMailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Mail::extend('brevo', function () {
            $apiKey = config('services.brevo.api_key');

            if (!$apiKey) {
                throw new \RuntimeException('BREVO_API_KEY is missing.');
            }

            return new BrevoTransport($apiKey);
        });
    }
}
