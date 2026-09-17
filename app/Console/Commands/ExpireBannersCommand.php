<?php

namespace App\Console\Commands;

use App\Models\Banner;
use Illuminate\Console\Command;

class ExpireBannersCommand extends Command
{
    protected $signature = 'expire:banners';

    protected $description = 'Expire banners whose expires_at date has passed';

    public function handle(): int
    {
        $expired = Banner::where('status', Banner::STATUS_ACTIVE)
            ->where('expires_at', '<', now())
            ->update(['status' => Banner::STATUS_CLOSED]);

        $this->info("Expired {$expired} banner(s).");

        return self::SUCCESS;
    }
}
