<?php

namespace App\Jobs\Customer;

use App\Services\DocumentService;
use Skillz\Nnpcreusable\Models\Customer;
use Illuminate\Bus\Queueable;
use Skillz\Nnpcreusable\Service\CustomerService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class CustomerCreated
 *
 * Handles the creation of a new customer and associated documents.
 *
 * @package App\Jobs\Customer
 */
class CustomerCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The data for creating the customer and document.
     *
     * @var array
     */
    public array $data;

    /**
     * Create a new job instance.
     *
     * @param array $data The data needed for creating the customer and document.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @param CustomerService $customerService The service responsible for creating customers.
     * @param DocumentService $documentService The service responsible for creating documents.
     * @return void
     */
    public function handle(CustomerService $customerService, DocumentService $documentService): void
    {
        $customerService->createCustomer($this->data);
        $documentService->create($this->data);
    }
}