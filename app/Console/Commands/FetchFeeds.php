<?php

namespace App\Console\Commands;

use App\Queue\QueueService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FetchFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:feeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private QueueService $queueService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(QueueService $queueService)
    {
        parent::__construct();
        $this->queueService = $queueService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \JsonException
     */
    public function handle(): int
    {
        $this->queueService->dispatch(explode(",", getenv("RSS_FEED_ARRAY")), 'rss', 'rss');

        return CommandAlias::SUCCESS;
    }
}
