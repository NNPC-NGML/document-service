<?php

namespace Tests\Unit\Jobs\CustomerSite;

use Tests\TestCase;
use App\Jobs\CustomerSite\CustomerSiteUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Skillz\Nnpcreusable\Service\CustomerService;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Queue;
use Skillz\Nnpcreusable\Models\CustomerSite;

class CustomerSiteUpdatedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function testCustomerSiteUpdatedSuccess()
    {

        // Updated data
        $updatedData = [
            'id' => 20,
            'site_name' => "Test Site Name Updated",
            'customer_id' => "20",
            'site_email' => 'jane@example.com',
            'site_address' => "35 Suite",
            'site_state_id' => 15,
            'site_lga_id' => 10,
            'site_zone_id' => 12,
            'is_active' => 0,
            // other updated fields
            'entity_id' => 1,
            'entity_name' => 'TestEntity',
            'tag' => 'TestTag',
            'file_name' => 'testfile.txt',
            'file_content' => base64_encode('Test file content')
        ];

        // Create the job instance
        $job = new CustomerSiteUpdated($updatedData);

        // Get real instances of the services
        $documentService = app(DocumentService::class);

        // Run the job
        $job->handle($documentService);

        // Adjust these assertions based on your actual DocumentService implementation
        $this->assertDatabaseHas('documents', ['entity_id' => $updatedData['entity_id'], 'entity_name' => $updatedData['entity_name']]);
    }
}
