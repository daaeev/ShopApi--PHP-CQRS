<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Project\Common\CQRS\ApplicationMessagesManager;
use Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Eloquent;
use Project\Modules\Shopping\Discounts\Promotions\Commands\RefreshPromotionStatusCommand;

class RefreshPromotionsStatusCommand extends Command
{
    protected $signature = 'app-promotions:refresh-statuses';
    protected $description = 'Refresh promotions statuses';

    public function __construct(
        private ApplicationMessagesManager $messagesManager
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $startTime = Carbon::now();
        $notRefreshedPromotions = Eloquent\Promotion::query()
            ->whereStatusDoesNotRefreshed()
            ->get();

        foreach ($notRefreshedPromotions as $notRefreshedPromotion) {
            $refreshStatusCommand = new RefreshPromotionStatusCommand($notRefreshedPromotion->id);
            $this->messagesManager->dispatchCommand($refreshStatusCommand);
            $this->comment('Promotion ' . $notRefreshedPromotion->id . ' status refreshed');
        }

        $executionTime = Carbon::now()->diffInSeconds($startTime);
        $this->info('Promotions status refreshed. Execution time: ' . $executionTime . '`s');
    }
}