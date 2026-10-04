<?php

namespace Tests\Feature;

use App\Models\FinanceAccount;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\Mosque;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Mosque $mosque;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mosque = Mosque::create([
            'name' => 'Masjid Test',
            'slug' => 'masjid-test',
            'domain' => 'test.masjid.com',
        ]);
        
        $this->user = User::factory()->create([
            'mosque_id' => $this->mosque->id,
        ]);
        
        $role = \App\Models\Role::create([
            'mosque_id' => $this->mosque->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);
        
        $this->user->roles()->attach($role);
    }

    public function test_it_can_list_finance_transactions(): void
    {
        FinanceTransaction::create([
            'mosque_id' => $this->mosque->id,
            'title' => 'Test',
            'amount' => 1000,
            'entry_type' => 'income',
            'transaction_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
            'reference_no' => 'TRX-123'
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.index'));

        $response->assertStatus(200);
    }

    public function test_it_can_create_a_finance_transaction(): void
    {
        $account = FinanceAccount::create([
            'mosque_id' => $this->mosque->id,
            'name' => 'Kas Utama',
            'type' => 'cash'
        ]);
        
        $category = FinanceCategory::create([
            'mosque_id' => $this->mosque->id,
            'name' => 'Infaq',
            'entry_type' => 'income'
        ]);

        $response = $this->actingAs($this->user)
            ->from(route('finance.index'))
            ->post(route('finance.store'), [
                'finance_account_id' => $account->id,
                'finance_category_id' => $category->id,
                'title' => 'Kotak Amal Jumat',
                'amount' => 500000,
                'entry_type' => 'income',
                'transaction_date' => now()->toDateString(),
                'status' => 'approved',
                'payment_method' => 'cash',
        ]);

        $response->assertRedirect(route('finance.index'));
        $this->assertDatabaseHas('finance_transactions', [
            'mosque_id' => $this->mosque->id,
            'title' => 'Kotak Amal Jumat',
            'amount' => 500000,
        ]);
    }

    public function test_it_prevents_viewing_other_mosque_transactions(): void
    {
        $otherMosque = Mosque::create([
            'name' => 'Other',
            'slug' => 'other',
            'domain' => 'other.com'
        ]);
        
        $otherUser = User::factory()->create(['mosque_id' => $otherMosque->id]);
        
        $otherTransaction = FinanceTransaction::create([
            'mosque_id' => $otherMosque->id,
            'title' => 'Test',
            'amount' => 1000,
            'entry_type' => 'income',
            'transaction_date' => now(),
            'status' => 'approved',
            'created_by' => $otherUser->id,
            'reference_no' => 'TRX-456'
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.show', $otherTransaction->id));

        $response->assertStatus(404);
    }
}
