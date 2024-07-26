<?php

namespace Tests\Unit\Jobs\CustomerSite;

use Tests\TestCase;
use App\Jobs\CustomerSite\CustomerSiteCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\DocumentService;

class CustomerSiteCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function testCustomerSiteCreatedSuccess()
    {
        // Prepare test data
        $data = [
            'id' => 20,
            'site_name' => "Test Site Name",
            'customer_id' => "20",
            'site_email' => 'john@example.com',
            'site_address' => "35 Suite",
            'site_state_id' => 15,
            'site_lga_id' => 10,
            'site_zone_id' => 12,
            'is_active' => 1,
            // other necessary data fields
            'entity_id' => 1,
            'entity_name' => 'TestEntity',
            'tag' => 'TestTag',
            'file_name' => 'testfile.txt',
            'file_content' => base64_encode('Test file content')
        ];

        // Ensure the database starts empty
        $this->assertDatabaseMissing('documents', ['entity_id' => $data['entity_id']]);

        // Instantiate services
        $documentService = app(DocumentService::class);

        // Create the job instance
        $job = new CustomerSiteCreated($data);

        // Run the job
        $job->handle($documentService);

        // Assertions to verify the job worked as expected
        $this->assertDatabaseHas('documents', ['entity_id' => $data['entity_id']]);
    }
}
