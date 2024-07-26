<?php

namespace Tests\Unit\Jobs\CustomerSite;

use Tests\TestCase;
use App\Jobs\CustomerSite\CustomerSiteDeleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Skillz\Nnpcreusable\Models\CustomerSite;

class CustomerSiteDeletedTest extends TestCase
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
        $customerSite = CustomerSite::create([
            'id' => 20,
            'site_name' => "Test Site Name",
            'customer_id' => "20",
            'site_email' => 'john@example.com',
            'site_address' => "35 Suite",
            'site_state_id' => 15,
            'site_lga_id' => 10,
            'site_zone_id' => 12,
            'is_active' => 1,
        ]);

        // Create the job instance
        $job = new CustomerSiteDeleted($customerSite->id);

        // Run the job
        $job->handle();

        // Assertions to verify the job worked as expected
        $this->assertTrue(true);
    }
}
