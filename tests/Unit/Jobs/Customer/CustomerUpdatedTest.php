<?php

namespace Tests\Unit\Jobs\Customer;

use Tests\TestCase;
use App\Jobs\Customer\CustomerUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\DocumentService;
use Skillz\Nnpcreusable\Models\Customer;
use Illuminate\Support\Facades\Queue;

class CustomerUpdatedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function testCustomerUpdatedSuccess()
    {
        // Prepare test data
        $customer = Customer::create([
            'company_name' => 'John Doe Company',
            'company_email' => 'john@example.com',
        ]);

        // Updated data
        $updatedData = [
            'id' => $customer->id,
            'company_name' => 'Jane Doe Company',
            'company_email' => 'jane@example.com',
            // other updated fields
            'entity_id' => 20,
            'entity_name' => 'TestEntity',
            'tag' => 'TestTag',
            'file_name' => 'testfile.txt',
            'file_content' => base64_encode('Test file content')
        ];

        // Create the job instance
        $job = new CustomerUpdated($updatedData);

        // Get real instances of the services
        $documentService = app(DocumentService::class);

        // Run the job
        $job->handle($documentService);

        // Assertions to verify the job worked as expected
        // Adjust these assertions based on your actual DocumentService implementation
        $this->assertDatabaseHas('documents', ['entity_id' => $updatedData['entity_id'], 'entity_name' => $updatedData['entity_name']]);
    }
}
