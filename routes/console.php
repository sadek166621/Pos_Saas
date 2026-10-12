<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('saas:check-subscriptions', function () {
    $this->info('Starting SaaS subscription lifecycle checks (Reminders, Grace Periods, Expiries)...');
    $service = app(\App\Services\SubscriptionService::class);
    $service->processExpiringSubscriptions();
    $this->info('SaaS subscription checks completed successfully.');
})->purpose('Check expiring tenant subscriptions and process grace periods or suspensions');

\Illuminate\Support\Facades\Schedule::command('saas:check-subscriptions')->daily();

