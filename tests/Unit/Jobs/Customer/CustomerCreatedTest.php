<?php

namespace Tests\Unit\Jobs\Customer;

use Tests\TestCase;
use App\Jobs\Customer\CustomerCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Skillz\Nnpcreusable\Service\CustomerService;
use App\Services\DocumentService;

class CustomerCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function testCustomerCreatedSuccess()
    {
        // Prepare test data
        $data = [
            'company_name' => 'John Doe Company',
            'company_email' => 'john@example.com',
            // other necessary data fields
            'entity_id' => 1,
            'entity_name' => 'TestEntity',
            'tag' => 'TestTag',
            'file_name' => 'testfile.txt',
            'file_content' => base64_encode('Test file content')
        ];

        // Ensure the database starts empty
        $this->assertDatabaseMissing('customers', ['company_email' => $data['company_email']]);
        $this->assertDatabaseMissing('documents', ['entity_id' => $data['entity_id']]);

        // Instantiate services
        $customerService = app(CustomerService::class);
        $documentService = app(DocumentService::class);

        // Create the job instance
        $job = new CustomerCreated($data);

        // Run the job
        $job->handle($customerService, $documentService);

        // Assertions to verify the job worked as expected
        $this->assertDatabaseHas('customers', ['company_email' => $data['company_email']]);
        $this->assertDatabaseHas('documents', ['entity_id' => $data['entity_id']]);
    }
}
