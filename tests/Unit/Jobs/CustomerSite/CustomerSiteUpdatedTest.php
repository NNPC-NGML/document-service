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
        // Prepare test data
        $customerSite = CustomerSite::create([
            'id' => 20,
            'site_name' => "Test Site Name",
            'customer_id' => "20",
            'site_email' => 'john@example.com',
            'site_address' => "35 Suite",
            'site_state_id' => 15,
            'site_lga_id' => 10,
            'site_zone_id' => 12,
            'is_active' => 0,
        ]);

        // Updated data
        $updatedData = [
            'id' => $customerSite->id,
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

        // Check that the customer site and associated documents exist before running the job
        $this->assertDatabaseHas('customer_sites', ['id' => $customerSite->id, 'site_name' => $customerSite->site_name]);

        // Create the job instance
        $job = new CustomerSiteUpdated($updatedData);

        // Get real instances of the services
        $customerService = app(CustomerService::class);
        $documentService = app(DocumentService::class);

        // Run the job
        $job->handle($customerService, $documentService);

        // Assertions to verify the job worked as expected
        $this->assertDatabaseHas('customer_sites', ['id' => $customerSite->id, 'site_name' => $updatedData['site_name']]);
        $this->assertDatabaseHas('customer_sites', ['id' => $customerSite->id, 'site_email' => $updatedData['site_email']]);
        // Adjust these assertions based on your actual DocumentService implementation
        $this->assertDatabaseHas('documents', ['entity_id' => $updatedData['entity_id'], 'entity_name' => $updatedData['entity_name']]);
    }
}
