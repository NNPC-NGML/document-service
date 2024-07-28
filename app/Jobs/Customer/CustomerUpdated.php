<?php

namespace App\Jobs\Customer;

use App\Services\DocumentService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class CustomerUpdated
 *
 * Handles the update of a customer and associated documents.
 *
 * @package App\Jobs\Customer
 */
class CustomerUpdated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The data for updating the customer.
     *
     * @var array
     */
    private array $data;

    /**
     * The ID of the customer to be updated.
     *
     * @var int
     */
    private int $id;

    /**
     * Create a new job instance.
     *
     * @param array $data The data needed for updating the customer, including the ID.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data['id'];
    }

    /**
     * Execute the job.
     *
     * @param DocumentService $documentService The service responsible for updating documents.
     * @return void
     */
    public function handle(DocumentService $documentService): void
    {
        $documentService->update($this->data);
    }
}
