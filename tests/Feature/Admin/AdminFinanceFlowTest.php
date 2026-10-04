<?php

namespace Tests\Feature\Admin;

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

class AdminFinanceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminMvpSeeder::class);
    }

    public function test_bendahara_can_create_finance_transaction_with_approval_metadata(): void
    {
        $user = $this->userWithRole('bendahara');
        $accountId = FinanceAccount::query()
            ->where('mosque_id', $user->mosque_id)
            ->where('name', 'Kas Utama')
            ->value('id');
        $categoryId = FinanceCategory::query()
            ->where('mosque_id', $user->mosque_id)
            ->where('name', 'Infaq Harian')
            ->value('id');

        $response = $this->actingAs($user)->post(route('finance.store'), [
            'title' => 'Penerimaan Infaq Jumat',
            'entry_type' => 'income',
            'finance_account_id' => $accountId,
            'finance_category_id' => $categoryId,
            'amount' => 1750000,
            'payment_method' => 'cash',
            'status' => 'approved',
            'transaction_date' => now()->toDateString(),
            'notes' => 'Dihitung setelah shalat Jumat.',
        ]);

        $response->assertRedirect();

        $transaction = FinanceTransaction::query()
            ->where('title', 'Penerimaan Infaq Jumat')
            ->latest('id')
            ->first();

        $this->assertNotNull($transaction);
        $this->assertSame($user->mosque_id, $transaction->mosque_id);
        $this->assertSame('approved', $transaction->status);
        $this->assertSame($user->id, $transaction->created_by);
        $this->assertSame($user->id, $transaction->approved_by);
        $this->assertNotNull($transaction->approved_at);
        $this->assertStringStartsWith('FIN-', $transaction->reference_no);
    }

    public function test_bendahara_can_approve_existing_finance_transaction(): void
    {
        $user = $this->userWithRole('bendahara');

        $transaction = FinanceTransaction::query()->create([
            'mosque_id' => $user->mosque_id,
            'finance_account_id' => FinanceAccount::query()
                ->where('mosque_id', $user->mosque_id)
                ->where('name', 'Rekening Operasional')
                ->value('id'),
            'finance_category_id' => FinanceCategory::query()
                ->where('mosque_id', $user->mosque_id)
                ->where('name', 'Operasional Masjid')
                ->value('id'),
            'entry_type' => 'expense',
            'title' => 'Pembelian Lampu Cadangan',
            'transaction_date' => now()->toDateString(),
            'amount' => 325000,
            'status' => 'pending',
            'payment_method' => 'transfer',
            'reference_no' => 'FIN-TEST-PENDING',
            'notes' => 'Menunggu validasi bendahara.',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->patch(route('finance.update-status', $transaction), [
            'status' => 'approved',
        ]);

        $response->assertRedirect();

        $transaction->refresh();

        $this->assertSame('approved', $transaction->status);
        $this->assertSame($user->id, $transaction->approved_by);
        $this->assertNotNull($transaction->approved_at);
    }

    public function test_bendahara_can_update_pending_finance_transaction(): void
    {
        $user = $this->userWithRole('bendahara');

        $transaction = $this->makeTransaction($user, [
            'status' => 'pending',
            'title' => 'Biaya Konsumsi Awal',
            'reference_no' => 'FIN-EDIT-PENDING',
        ]);

        $response = $this->actingAs($user)->put(route('finance.update', $transaction), [
            'title' => 'Biaya Konsumsi Revisi',
            'entry_type' => 'expense',
            'finance_account_id' => $transaction->finance_account_id,
            'finance_category_id' => $transaction->finance_category_id,
            'amount' => 450000,
            'payment_method' => 'transfer',
            'status' => 'pending',
            'transaction_date' => now()->toDateString(),
            'notes' => 'Direvisi oleh bendahara.',
        ]);

        $response->assertRedirect(route('finance.show', $transaction));

        $transaction->refresh();
        $this->assertSame('Biaya Konsumsi Revisi', $transaction->title);
        $this->assertSame('450000.00', $transaction->amount);
    }

    public function test_approved_finance_transaction_cannot_be_edited(): void
    {
        $user = $this->userWithRole('bendahara');

        $transaction = $this->makeTransaction($user, [
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'reference_no' => 'FIN-LOCKED-APPROVED',
        ]);

        $response = $this->actingAs($user)->get(route('finance.edit', $transaction));

        $response->assertForbidden();
    }

    public function test_bendahara_cannot_delete_finance_transaction_without_delete_permission(): void
    {
        $user = $this->userWithRole('bendahara');
        $transaction = $this->makeTransaction($user, [
            'status' => 'pending',
            'reference_no' => 'FIN-NO-DELETE-PERM',
        ]);

        $response = $this->actingAs($user)->delete(route('finance.destroy', $transaction));

        $response->assertForbidden();
        $this->assertDatabaseHas('finance_transactions', ['id' => $transaction->id]);
    }

    public function test_super_admin_cannot_delete_approved_finance_transaction(): void
    {
        $user = $this->userWithRole('super-admin');

        $transaction = $this->makeTransaction($user, [
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'reference_no' => 'FIN-NO-DELETE',
        ]);

        $response = $this->actingAs($user)->delete(route('finance.destroy', $transaction));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('finance_transactions', ['id' => $transaction->id]);
    }

    public function test_super_admin_can_delete_pending_finance_transaction(): void
    {
        $user = $this->userWithRole('super-admin');

        $transaction = $this->makeTransaction($user, [
            'status' => 'pending',
            'reference_no' => 'FIN-DELETE-PENDING',
        ]);

        $response = $this->actingAs($user)->delete(route('finance.destroy', $transaction));

        $response->assertRedirect(route('finance.index'));
        $this->assertDatabaseMissing('finance_transactions', ['id' => $transaction->id]);
    }

    private function makeTransaction(User $user, array $overrides = []): FinanceTransaction
    {
        return FinanceTransaction::query()->create(array_merge([
            'mosque_id' => $user->mosque_id,
            'finance_account_id' => FinanceAccount::query()
                ->where('mosque_id', $user->mosque_id)
                ->where('name', 'Rekening Operasional')
                ->value('id'),
            'finance_category_id' => FinanceCategory::query()
                ->where('mosque_id', $user->mosque_id)
                ->where('name', 'Operasional Masjid')
                ->value('id'),
            'entry_type' => 'expense',
            'title' => 'Transaksi Uji',
            'transaction_date' => now()->toDateString(),
            'amount' => 325000,
            'status' => 'pending',
            'payment_method' => 'transfer',
            'reference_no' => 'FIN-TEST-BASE',
            'notes' => 'Catatan pengujian.',
            'created_by' => $user->id,
        ], $overrides));
    }

    private function userWithRole(string $roleSlug): User
    {
        $user = User::factory()->create([
            'mosque_id' => Mosque::query()->where('slug', 'default-masjid')->value('id'),
        ]);

        $user->roles()->sync([Role::query()->where('slug', $roleSlug)->value('id')]);

        return $user;
    }
}
