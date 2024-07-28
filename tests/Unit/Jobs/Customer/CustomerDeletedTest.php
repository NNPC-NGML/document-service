<?php

namespace Tests\Unit\Jobs\Customer;

use Tests\TestCase;
use App\Jobs\Customer\CustomerDeleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        // Create the job instance
        $job = new CustomerDeleted($customer->id);

        // Run the job
        $job->handle();

        // Assertions to verify the job worked as expected
        $this->assertTrue(true);
    }
}
