<?php

namespace Tests\Unit\Jobs\Customer;

use Tests\TestCase;
use App\Jobs\Customer\CustomerDeleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Skillz\Nnpcreusable\Service\CustomerService;
use App\Services\DocumentService;
use Skillz\Nnpcreusable\Models\Customer;
use Illuminate\Support\Facades\Queue;

class CustomerDeletedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function testCustomerDeletedSuccess()
    {
        // Prepare test data
        $customer = Customer::create([
            'company_name' => 'John Doe Company',
            'company_email' => 'john@example.com',
        ]);

        // Assuming DocumentService expects some document related to the customer
        // Here you would need to adjust according to your actual DocumentService implementation
        // Example:
        // Document::create(['customer_id' => $customer->id, 'file_name' => 'document1.pdf']);

        // Check that the customer and associated documents exist before running the job
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        // $this->assertDatabaseHas('documents', ['customer_id' => $customer->id]);

        // Create the job instance
        $job = new CustomerDeleted($customer->id);

        // Get real instances of the services
        $customerService = app(CustomerService::class);
        $documentService = app(DocumentService::class);

        // Run the job
        $job->handle($customerService, $documentService);

        // Assertions to verify the job worked as expected
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
        // $this->assertDatabaseMissing('documents', ['customer_id' => $customer->id]);
    }
}
