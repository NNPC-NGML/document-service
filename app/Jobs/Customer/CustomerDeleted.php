<?php

namespace App\Jobs\Customer;

use App\Services\DocumentService;
use Illuminate\Bus\Queueable;
use Skillz\Nnpcreusable\Service\CustomerService;
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
     * @param CustomerService $customerService The service responsible for deleting customers.
     * @param DocumentService $documentService The service responsible for deleting documents.
     * @return void
     */
    public function handle(CustomerService $customerService, DocumentService $documentService): void
    {
        $customerService->destroyCustomer($this->id);
        $documentService->delete($this->id);
    }
}
