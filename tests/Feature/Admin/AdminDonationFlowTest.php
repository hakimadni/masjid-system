<?php

namespace Tests\Feature\Admin;

use App\Models\Donation;
use App\Models\FinanceAccount;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminMvpSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDonationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminMvpSeeder::class);
    }

    public function test_confirmed_donation_creates_linked_finance_transaction(): void
    {
        $mosqueId = Mosque::query()->where('slug', 'default-masjid')->value('id');
        $user = User::factory()->create(['mosque_id' => $mosqueId]);
        $user->roles()->sync([Role::query()->where('slug', 'bendahara')->value('id')]);

        $response = $this->actingAs($user)->post(route('donations.store'), [
            'donor_name' => 'Hamba Allah',
            'donation_category_id' => null,
            'campaign' => 'Renovasi Karpet',
            'amount' => 500000,
            'method' => 'transfer',
            'status' => 'confirmed',
            'donation_date' => now()->toDateString(),
            'phone' => '08123456789',
            'notes' => 'Transfer mobile banking',
            'is_anonymous' => false,
        ]);

        $response->assertRedirect();

        $donation = Donation::query()
            ->where('campaign', 'Renovasi Karpet')
            ->latest('id')
            ->first();
        $this->assertNotNull($donation);
        $this->assertSame('confirmed', $donation->status);
        $this->assertNotNull($donation->finance_transaction_id);

        $financeTransaction = FinanceTransaction::query()->find($donation->finance_transaction_id);
        $this->assertNotNull($financeTransaction);
        $this->assertSame('income', $financeTransaction->entry_type);
        $this->assertSame('approved', $financeTransaction->status);
        $this->assertSame('transfer', $financeTransaction->payment_method);
    }

    public function test_admin_mvp_seeder_prepares_finance_reference_data(): void
    {
        $this->assertDatabaseCount('finance_accounts', 2);
        $this->assertGreaterThanOrEqual(2, FinanceCategory::query()->where('entry_type', 'income')->count());
        $this->assertNotNull(FinanceAccount::query()->where('name', 'Kas Utama')->first());
    }
}
