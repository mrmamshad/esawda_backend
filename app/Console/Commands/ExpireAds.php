<?php

namespace App\Console\Commands;

use App\Services\AdService;
use Illuminate\Console\Command;

/**
 * Mark every active ad whose expire_date has passed as `expire`.
 * Run daily via the scheduler; idempotent (only touches active ads).
 */
class ExpireAds extends Command
{
    protected $signature   = 'ads:expire';
    protected $description = 'Automatically expire classified ads past their expire_date';

    public function handle(AdService $ads): int
    {
        $count = $ads->expireDueAds();
        $this->info("Expired {$count} ad(s).");
        return self::SUCCESS;
    }
}