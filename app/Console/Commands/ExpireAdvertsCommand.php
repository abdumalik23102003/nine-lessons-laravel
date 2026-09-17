<?php

namespace App\Console\Commands;

use App\Models\Advert;
use Illuminate\Console\Command;

class ExpireAdvertsCommand extends Command
{
    protected $signature = 'expire:adverts';

    protected $description = 'Expire adverts whose expires_at date has passed';

    public function handle(): int
    {
        $expired = Advert::where('status', Advert::STATUS_ACTIVE)
            ->where('expires_at', '<', now())
            ->update(['status' => Advert::STATUS_CLOSED]);

        $this->info("Expired {$expired} advert(s).");

        return self::SUCCESS;
    }
}
