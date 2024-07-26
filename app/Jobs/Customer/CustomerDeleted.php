<?php

namespace App\Jobs\Customer;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class CustomerDeleted
 *
 * Handles the deletion of a customer and associated documents.
 *
 * @package App\Jobs\Customer
 */
class CustomerDeleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The ID of the customer to delete.
     *
     * @var int
     */
    private int $id;

    /**
     * Create a new job instance.
     *
     * @param int $id The ID of the customer to be deleted.
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
    }
}
