<?php

namespace Tests\Unit\Jobs\Customer;

use Tests\TestCase;
use App\Jobs\Customer\CustomerUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Skillz\Nnpcreusable\Service\CustomerService;
use App\Services\DocumentService;
use Skillz\Nnpcreusable\Models\Customer;
use Illuminate\Support\Facades\Queue;

class CustomerUpdatedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake(); // Optionally, fake the queue if you want to test that jobs are dispatched correctly
    }

    public function testHandle()
    {
        // Prepare test data
        $customer = Customer::create([
            'company_name' => 'John Doe Company',
            'company_email' => 'john@example.com',
            // other necessary fields
        ]);

        // Assuming DocumentService expects some document related to the customer
        // Here you would need to adjust according to your actual DocumentService implementation
        // Example:
        // Document::create(['customer_id' => $customer->id, 'file_name' => 'document1.pdf']);

        // Updated data
        $updatedData = [
            'id' => $customer->id,
            'company_name' => 'Jane Doe Company',
            'company_email' => 'jane@example.com',
            // other updated fields
        ];

        // Check that the customer and associated documents exist before running the job
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'company_name' => 'John Doe Company']);
        // $this->assertDatabaseHas('documents', ['customer_id' => $customer->id, 'file_name' => 'document1.pdf']);

        // Create the job instance
        $job = new CustomerUpdated($updatedData);

        // Get real instances of the services
        $customerService = app(CustomerService::class);
        $documentService = app(DocumentService::class);

        // Run the job
        $job->handle($customerService, $documentService);

        // Assertions to verify the job worked as expected
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'company_name' => 'Jane Doe Company']);
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'company_email' => 'jane@example.com']);
        // Adjust these assertions based on your actual DocumentService implementation
        // $this->assertDatabaseHas('documents', ['customer_id' => $customer->id, 'file_name' => 'document1.pdf']);
    }
}
