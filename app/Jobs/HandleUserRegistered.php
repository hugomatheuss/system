<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleUserRegistered implements ShouldQueue
{
    use Queueable;

    /** @var array<string,mixed> */
    protected array $data;

    /**
     * @param  array<string,mixed>  $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('Processing user.registered job', $this->data);
    }
}
