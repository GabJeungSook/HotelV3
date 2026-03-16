<?php

namespace Tests\Feature\BackOffice;

use App\Http\Livewire\BackOffice\SalesReportV2;
use App\Models\Branch;
use App\Models\CheckinDetail;
use App\Models\Floor;
use App\Models\Frontdesk;
use App\Models\Guest;
use App\Models\Rate;
use App\Models\Room;
use App\Models\ShiftLog;
use App\Models\StayingHour;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesReportV2Test extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Branch $branch;
    protected Floor $floor;
    protected Type $roomType;
    protected Room $room;
    protected Rate $rate;
    protected Frontdesk $frontdesk;
    protected ShiftLog $shiftLog;
    protected StayingHour $stayingHour;

    protected function setUp(): void
    {
        parent::setUp();

        // Create branch
        $this->branch = Branch::create([
            'name' => 'Test Branch',
        ]);

        // Create user with branch
        $this->user = User::factory()->create([
            'branch_id' => $this->branch->id,
            'branch_name' => $this->branch->name,
        ]);

        // Create floor
        $this->floor = Floor::create([
            'branch_id' => $this->branch->id,
            'number' => 1,
        ]);

        // Create room type
        $this->roomType = Type::create([
            'branch_id' => $this->branch->id,
            'name' => 'Standard',
        ]);

        // Create room
        $this->room = Room::create([
            'branch_id' => $this->branch->id,
            'floor_id' => $this->floor->id,
            'type_id' => $this->roomType->id,
            'number' => 101,
            'status' => 'available',
        ]);

        // Create staying hour
        $this->stayingHour = StayingHour::create([
            'branch_id' => $this->branch->id,
            'number' => 3,
        ]);

        // Create rate
        $this->rate = Rate::create([
            'branch_id' => $this->branch->id,
            'type_id' => $this->roomType->id,
            'staying_hour_id' => $this->stayingHour->id,
            'amount' => 500,
            'is_available' => true,
        ]);

        // Create frontdesk
        $this->frontdesk = Frontdesk::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'name' => 'Test Frontdesk',
            'number' => 1,
        ]);

        // Create shift log
        $this->shiftLog = ShiftLog::create([
            'frontdesk_id' => $this->user->id,
            'frontdesk_ids' => json_encode([$this->user->id]),
            'time_in' => now()->setTime(8, 0),
            'shift' => 'AM',
        ]);

        // Seed transaction types if not exists
        $this->seedTransactionTypes();
    }

    protected function seedTransactionTypes(): void
    {
        $types = [
            ['id' => 1, 'name' => 'Check In'],
            ['id' => 2, 'name' => 'Deposit'],
            ['id' => 4, 'name' => 'Damage Charges'],
            ['id' => 5, 'name' => 'Cashout'],
            ['id' => 6, 'name' => 'Extend'],
            ['id' => 7, 'name' => 'Transfer Room'],
            ['id' => 8, 'name' => 'Amenities'],
            ['id' => 9, 'name' => 'Food and Beverages'],
        ];

        foreach ($types as $type) {
            TransactionType::firstOrCreate(['id' => $type['id']], $type);
        }
    }

    /**
     * Create a guest with all required fields.
     */
    protected function createGuest(array $overrides = []): Guest
    {
        return Guest::create(array_merge([
            'branch_id' => $this->branch->id,
            'room_id' => $this->room->id,
            'rate_id' => $this->rate->id,
            'type_id' => $this->roomType->id,
            'static_amount' => 500,
            'name' => 'Test Guest',
            'qr_code' => 'QR' . uniqid(),
        ], $overrides));
    }

    /**
     * Create a checkin detail with all required fields.
     */
    protected function createCheckinDetail(Guest $guest, array $overrides = []): CheckinDetail
    {
        return CheckinDetail::create(array_merge([
            'guest_id' => $guest->id,
            'type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'rate_id' => $this->rate->id,
            'static_amount' => 500,
            'hours_stayed' => 3,
            'check_in_at' => now()->setTime(10, 0),
            'check_out_at' => now()->addHours(3),
            'is_long_stay' => false,
        ], $overrides));
    }

    /**
     * Create a transaction with all required fields.
     */
    protected function createTransaction(Guest $guest, CheckinDetail $checkinDetail, array $overrides = []): Transaction
    {
        return Transaction::create(array_merge([
            'branch_id' => $this->branch->id,
            'room_id' => $this->room->id,
            'guest_id' => $guest->id,
            'floor_id' => $this->floor->id,
            'transaction_type_id' => 1,
            'assigned_frontdesk_id' => json_encode([$this->user->id]),
            'description' => 'Room Charge',
            'remarks' => '',
            'shift_log_id' => $this->shiftLog->id,
            'checkin_detail_id' => $checkinDetail->id,
            'payable_amount' => 500,
            'paid_amount' => 500,
        ], $overrides));
    }

    /** @test */
    public function component_can_render()
    {
        $this->actingAs($this->user);

        Livewire::test(SalesReportV2::class)
            ->assertStatus(200)
            ->assertSee('SALES REPORT V2');
    }

    /** @test */
    public function shows_transactions_for_guests_occupying_rooms_during_date_range()
    {
        $this->actingAs($this->user);

        // Create guest who checked in YESTERDAY but is still staying TODAY
        $guest = $this->createGuest(['name' => 'Test Guest Forward']);

        $checkinDetail = $this->createCheckinDetail($guest, [
            'check_in_at' => now()->subDay()->setTime(22, 0), // Yesterday 10 PM
            'check_out_at' => now()->addHours(4), // Today + 4 hours (still staying)
        ]);

        // Create extension transaction TODAY for guest who checked in yesterday
        // This tests that forward guests' transactions appear when created within date range
        $this->createTransaction($guest, $checkinDetail, [
            'transaction_type_id' => 6, // Extension
            'description' => 'Extension',
            'created_at' => now()->setTime(8, 0), // Today morning
        ]);

        // Test: Filter for TODAY only
        // The guest checked in yesterday (forward guest), but has transaction today
        // SalesReportV2 should show the transaction and mark guest as FORWARDED
        $component = Livewire::test(SalesReportV2::class)
            ->set('date_from', now()->toDateString())
            ->set('date_to', now()->toDateString())
            ->call('generateReport');

        // The guest should appear with their TODAY transaction
        $component->assertSee('TEST GUEST FORWARD');

        // Guest should be marked as forwarded since check-in was yesterday
        $salesRows = $component->get('salesRows');
        $this->assertTrue($salesRows[0]['is_forwarded']);
    }

    /** @test */
    public function filters_by_frontdesk_who_processed_transaction()
    {
        $this->actingAs($this->user);

        // Create second frontdesk user
        $user2 = User::factory()->create([
            'branch_id' => $this->branch->id,
            'branch_name' => $this->branch->name,
        ]);
        $frontdesk2 = Frontdesk::create([
            'branch_id' => $this->branch->id,
            'user_id' => $user2->id,
            'name' => 'Second Frontdesk',
            'number' => 2,
        ]);
        $shiftLog2 = ShiftLog::create([
            'frontdesk_id' => $user2->id,
            'frontdesk_ids' => json_encode([$user2->id]),
            'time_in' => now()->setTime(20, 0),
            'shift' => 'PM',
        ]);

        // Create guest
        $guest = $this->createGuest(['name' => 'Filter Test Guest']);
        $checkinDetail = $this->createCheckinDetail($guest);

        // Transaction processed by first frontdesk
        $this->createTransaction($guest, $checkinDetail, [
            'shift_log_id' => $this->shiftLog->id, // First frontdesk
        ]);

        // Transaction processed by second frontdesk
        $this->createTransaction($guest, $checkinDetail, [
            'transaction_type_id' => 6, // Extension
            'shift_log_id' => $shiftLog2->id, // Second frontdesk
            'payable_amount' => 200,
            'paid_amount' => 200,
            'description' => 'Extension',
        ]);

        // Filter by second frontdesk - should only see extension
        $component = Livewire::test(SalesReportV2::class)
            ->set('date_from', now()->toDateString())
            ->set('date_to', now()->toDateString())
            ->set('frontdesk', $user2->id)
            ->call('generateReport');

        // Should only show the extension (200), not the room charge (500)
        $this->assertEquals(200, $component->get('totalSales'));
    }

    /** @test */
    public function calculates_summary_by_transaction_type()
    {
        $this->actingAs($this->user);

        $guest = $this->createGuest(['name' => 'Summary Test Guest']);
        $checkinDetail = $this->createCheckinDetail($guest);

        // Room charge
        $this->createTransaction($guest, $checkinDetail, [
            'transaction_type_id' => 1,
            'payable_amount' => 500,
            'paid_amount' => 500,
        ]);

        // Extension
        $this->createTransaction($guest, $checkinDetail, [
            'transaction_type_id' => 6,
            'payable_amount' => 200,
            'paid_amount' => 200,
            'description' => 'Extension',
        ]);

        // Food
        $this->createTransaction($guest, $checkinDetail, [
            'transaction_type_id' => 9,
            'payable_amount' => 150,
            'paid_amount' => 150,
            'description' => 'Food',
        ]);

        $component = Livewire::test(SalesReportV2::class)
            ->set('date_from', now()->toDateString())
            ->set('date_to', now()->toDateString())
            ->call('generateReport');

        $summary = $component->get('summaryByType');

        $this->assertEquals(500, $summary['room_charges']);
        $this->assertEquals(200, $summary['extensions']);
        $this->assertEquals(150, $summary['food']);
        $this->assertEquals(850, $summary['grand_total']); // 500 + 200 + 150
    }

    /** @test */
    public function excludes_deposits_and_cashouts_from_total()
    {
        $this->actingAs($this->user);

        $guest = $this->createGuest(['name' => 'Deposit Test Guest']);
        $checkinDetail = $this->createCheckinDetail($guest);

        // Room charge (should count)
        $this->createTransaction($guest, $checkinDetail, [
            'transaction_type_id' => 1,
            'payable_amount' => 500,
            'paid_amount' => 500,
        ]);

        // Deposit (should NOT count in total)
        $this->createTransaction($guest, $checkinDetail, [
            'transaction_type_id' => 2,
            'payable_amount' => 300,
            'paid_amount' => 300,
            'description' => 'Deposit',
        ]);

        $component = Livewire::test(SalesReportV2::class)
            ->set('date_from', now()->toDateString())
            ->set('date_to', now()->toDateString())
            ->call('generateReport');

        // Total should be 500 (room charge only), not 800
        $this->assertEquals(500, $component->get('totalSales'));
    }

    /** @test */
    public function reset_filters_works()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(SalesReportV2::class)
            ->set('date_from', '2024-01-01')
            ->set('date_to', '2024-01-31')
            ->set('frontdesk', 999)
            ->call('resetFilters');

        $this->assertEquals(now()->toDateString(), $component->get('date_from'));
        $this->assertEquals(now()->toDateString(), $component->get('date_to'));
        $this->assertNull($component->get('frontdesk'));
    }
}
