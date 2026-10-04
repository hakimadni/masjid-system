<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\Mosque;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationControllerTest extends TestCase
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

    public function test_it_can_list_donations(): void
    {
        Donation::create([
            'mosque_id' => $this->mosque->id,
            'amount' => 500000,
            'status' => 'confirmed',
            'campaign' => 'Renovasi',
            'donation_date' => now(),
            'created_by' => $this->user->id,
            'reference_no' => 'DON-123'
        ]);

        $response = $this->actingAs($this->user)->get(route('donations.index'));

        $response->assertStatus(200);
    }

    public function test_it_can_create_a_donation(): void
    {
        $category = DonationCategory::create([
            'mosque_id' => $this->mosque->id,
            'name' => 'Zakat Fitrah',
        ]);

        $response = $this->actingAs($this->user)
            ->from(route('donations.index'))
            ->post(route('donations.store'), [
                'donor_name' => 'John Doe',
                'donation_category_id' => $category->id,
                'amount' => 150000,
                'status' => 'confirmed',
                'campaign' => 'Zakat 2026',
                'donation_date' => now()->toDateString(),
                'method' => 'cash',
                'is_anonymous' => false,
        ]);

        $response->assertRedirect(route('donations.index'));
        $this->assertDatabaseHas('donations', [
            'mosque_id' => $this->mosque->id,
            'amount' => 150000,
            'campaign' => 'Zakat Fitrah',
        ]);
    }

    public function test_it_prevents_viewing_other_mosque_donations(): void
    {
        $otherMosque = Mosque::create([
            'name' => 'Other',
            'slug' => 'other',
            'domain' => 'other.com'
        ]);
        
        $otherUser = User::factory()->create(['mosque_id' => $otherMosque->id]);
        
        $otherDonation = Donation::create([
            'mosque_id' => $otherMosque->id,
            'amount' => 20000,
            'status' => 'confirmed',
            'donation_date' => now(),
            'created_by' => $otherUser->id,
            'reference_no' => 'DON-456'
        ]);

        $response = $this->actingAs($this->user)->get(route('donations.show', $otherDonation->id));

        $response->assertStatus(404);
    }
}
