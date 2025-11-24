<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestBrevoMail extends Command
{
    protected $signature = 'brevo:test {to : Recipient email address} {--subject=Brevo Test : Email subject} {--html= : Optional HTML body}';
    protected $description = 'Send a test email via Brevo API transport to verify configuration.';

    public function handle(): int
    {
        $to = $this->argument('to');
        $subject = $this->option('subject');
        $html = $this->option('html');

        $from = config('mail.from.address');
        $apiKey = config('services.brevo.api_key');

        if (!$apiKey) {
            $this->error('BREVO_API_KEY is not set. Add it to .env and run config:clear.');
            return 1;
        }

        try {
            Mail::send([], [], function ($m) use ($to, $subject, $html) {
                $m->to($to)->subject($subject);
                $m->text('Brevo test plain content OK.');
                $m->html($html ?: '<p>Brevo test plain content OK.</p>');
            });
            $this->info("Test email dispatched to {$to}. Check inbox (and spam) within a minute.");
            return 0;
        } catch (\Throwable $e) {
            $this->error('Failed sending test email: ' . $e->getMessage());
            return 1;
        }
    }
}
