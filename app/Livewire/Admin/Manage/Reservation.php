<?php

namespace App\Livewire\Admin\Manage;

use Carbon\Carbon;
use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use WireUi\Traits\WireUiActions;
use App\Models\TemporaryReserved;
use App\Models\ActivityLog;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Rate;
use App\Models\StayingHour;
use App\Models\CheckinDetail;
use App\Models\Transaction;
use App\Models\NewGuestReport;

class Reservation extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    protected function getTableQuery(): Builder
    {
        return TemporaryReserved::query()
            ->where('branch_id', auth()->user()->branch_id)
            ->with(['guest', 'room.type', 'guest.rates.stayingHour']);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('guest.qr_code')
                ->label('QR CODE')
                ->searchable()
                ->sortable()
                ->badge()
                ->color('gray'),
            TextColumn::make('guest.name')
                ->label('GUEST NAME')
                ->searchable()
                ->sortable(),
            TextColumn::make('guest.contact')
                ->label('CONTACT')
                ->searchable()
                ->sortable()
                ->placeholder('N/A'),
            TextColumn::make('room.number')
                ->label('ROOM')
                ->formatStateUsing(fn ($record) => 'Room #' . $record->room?->number)
                ->weight('bold')
                ->sortable(),
            TextColumn::make('room.type.name')
                ->label('TYPE')
                ->badge()
                ->color('info'),
            TextColumn::make('created_at')
                ->label('RESERVED AT')
                ->dateTime('M d, Y h:i A')
                ->sortable(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        $branchId = auth()->user()->branch_id;

        return [
            Action::make('new_reservation')
                ->label('New Reservation')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->button()
                ->modalHeading('New Reservation')
                ->modalWidth('xl')
                ->form($this->getGuestFormSchema($branchId))
                ->action(function (array $data) use ($branchId) {
                    $this->createReservation($data, $branchId);
                }),
            Action::make('checkin_co')
                ->label('Check-In C/O')
                ->icon('heroicon-o-arrow-right-start-on-rectangle')
                ->color('success')
                ->button()
                ->modalHeading('Direct Check-In (C/O Guest)')
                ->modalWidth('xl')
                ->form($this->getGuestFormSchema($branchId))
                ->action(function (array $data) use ($branchId) {
                    $this->createDirectCheckIn($data, $branchId);
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\DeleteAction::make()
                ->label('Cancel')
                ->button()
                ->size('sm')
                ->modalHeading('Cancel Reservation')
                ->modalDescription('This will cancel the reservation and make the room available again.')
                ->before(function ($record) {
                    Room::where('id', $record->room_id)->update(['status' => 'Available']);

                    if ($record->guest) {
                        $record->guest->delete();
                    }

                    ActivityLog::create([
                        'branch_id' => auth()->user()->branch_id,
                        'user_id' => auth()->id(),
                        'activity' => 'Cancel Reservation',
                        'description' => 'Cancelled reservation for Room #' . $record->room?->number,
                    ]);
                }),
        ];
    }

    // ── Shared Form Schema ──────────────────────────────────

    private function getGuestFormSchema(int $branchId): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('name')
                    ->label('Guest Name')
                    ->required(),
                TextInput::make('contact')
                    ->label('Contact Number')
                    ->placeholder('Optional'),
            ]),
            Grid::make(2)->schema([
                Select::make('room_id')
                    ->label('Room')
                    ->options(function () use ($branchId) {
                        return Room::where('branch_id', $branchId)
                            ->where('status', 'Available')
                            ->with(['floor', 'type'])
                            ->orderBy('number')
                            ->get()
                            ->mapWithKeys(fn ($room) => [
                                $room->id => 'Room #' . $room->number . ' — ' . ($room->type?->name ?? '') . ' (Floor ' . ($room->floor?->number ?? '') . ')',
                            ]);
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('rate_id', null)),
                Select::make('rate_id')
                    ->label('Rate')
                    ->options(function (callable $get) use ($branchId) {
                        $roomId = $get('room_id');
                        if (!$roomId) return [];

                        return Rate::where('room_id', $roomId)
                            ->where('branch_id', $branchId)
                            ->with('stayingHour')
                            ->get()
                            ->mapWithKeys(fn ($rate) => [
                                $rate->id => ($rate->stayingHour?->number ?? '?') . ' Hours — ₱' . number_format($rate->amount, 2),
                            ]);
                    })
                    ->required()
                    ->reactive(),
            ]),
            Grid::make(2)->schema([
                Toggle::make('is_long_stay')
                    ->label('Long Stay')
                    ->reactive(),
                TextInput::make('number_of_days')
                    ->label('Number of Days')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (callable $get) => $get('is_long_stay'))
                    ->required(fn (callable $get) => $get('is_long_stay')),
            ]),
        ];
    }

    // ── QR Code Generator ───────────────────────────────────

    private function generateQrCode(): string
    {
        $count = Guest::whereYear('created_at', Carbon::today()->year)->count() + 1;
        return auth()->user()->branch_id . today()->format('y') . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ── Create Reservation ──────────────────────────────────

    private function createReservation(array $data, int $branchId): void
    {
        $rate = Rate::find($data['rate_id']);

        DB::beginTransaction();

        $guest = Guest::create([
            'branch_id' => $branchId,
            'name' => $data['name'],
            'contact' => $data['contact'] ?? 'N/A',
            'qr_code' => $this->generateQrCode(),
            'room_id' => $data['room_id'],
            'rate_id' => $data['rate_id'],
            'type_id' => Room::find($data['room_id'])?->type_id,
            'static_amount' => $rate->amount,
            'is_long_stay' => $data['is_long_stay'] ?? false,
            'number_of_days' => $data['number_of_days'] ?? 0,
            'is_co' => true,
        ]);

        TemporaryReserved::create([
            'branch_id' => $branchId,
            'room_id' => $data['room_id'],
            'guest_id' => $guest->id,
        ]);

        Room::where('id', $data['room_id'])->update(['status' => 'Reserved']);

        ActivityLog::create([
            'branch_id' => $branchId,
            'user_id' => auth()->id(),
            'activity' => 'Create Reservation',
            'description' => 'Reserved Room #' . Room::find($data['room_id'])?->number . ' for ' . $data['name'],
        ]);

        DB::commit();

        $this->dialog()->success('Reservation Added', 'Room has been reserved successfully.');
    }

    // ── Direct Check-In C/O ─────────────────────────────────

    private function createDirectCheckIn(array $data, int $branchId): void
    {
        $rate = Rate::with('stayingHour')->find($data['rate_id']);
        $room = Room::find($data['room_id']);
        $stayingHours = $rate->stayingHour->number;
        $isLongStay = $data['is_long_stay'] ?? false;
        $numberOfDays = $data['number_of_days'] ?? 0;

        // Calculate extension reset cycle
        $extensionReset = auth()->user()->branch->extension_time_reset;
        $numberOfHours = $stayingHours;
        $nextExtensionIsOriginal = false;
        while ($numberOfHours >= $extensionReset) {
            $numberOfHours -= $extensionReset;
            $nextExtensionIsOriginal = true;
        }

        DB::beginTransaction();

        $guest = Guest::create([
            'branch_id' => $branchId,
            'name' => $data['name'],
            'contact' => $data['contact'] ?? 'N/A',
            'qr_code' => $this->generateQrCode(),
            'room_id' => $data['room_id'],
            'rate_id' => $data['rate_id'],
            'type_id' => $room->type_id,
            'static_amount' => $rate->amount,
            'is_long_stay' => $isLongStay,
            'number_of_days' => $numberOfDays,
            'is_co' => true,
        ]);

        $checkin = CheckinDetail::create([
            'guest_id' => $guest->id,
            'frontdesk_id' => 1,
            'type_id' => $room->type_id,
            'room_id' => $data['room_id'],
            'rate_id' => $data['rate_id'],
            'static_amount' => $rate->amount,
            'hours_stayed' => $isLongStay ? $stayingHours * $numberOfDays : $stayingHours,
            'total_deposit' => 0,
            'check_in_at' => now(),
            'check_out_at' => $isLongStay ? now()->addDays($numberOfDays) : now()->addHours($stayingHours),
            'is_long_stay' => $isLongStay,
            'number_of_hours' => $numberOfHours,
            'next_extension_is_original' => $nextExtensionIsOriginal ? 1 : 0,
        ]);

        $shift = (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM';
        $assignedFrontdesk = json_encode([1, 'Admin']);

        // Check-in transaction
        Transaction::create([
            'branch_id' => $branchId,
            'checkin_detail_id' => $checkin->id,
            'cash_drawer_id' => auth()->user()->cash_drawer_id,
            'room_id' => $data['room_id'],
            'guest_id' => $guest->id,
            'floor_id' => $room->floor_id,
            'transaction_type_id' => 1,
            'assigned_frontdesk_id' => $assignedFrontdesk,
            'description' => 'Guest Check In',
            'payable_amount' => $rate->amount,
            'paid_amount' => 0,
            'change_amount' => 0,
            'deposit_amount' => 0,
            'paid_at' => now(),
            'remarks' => 'Guest C/O Checked In at Room #' . $room->number,
            'is_co' => true,
            'shift' => $shift,
        ]);

        // Deposit transaction
        Transaction::create([
            'branch_id' => $branchId,
            'checkin_detail_id' => $checkin->id,
            'cash_drawer_id' => auth()->user()->cash_drawer_id,
            'room_id' => $data['room_id'],
            'guest_id' => $guest->id,
            'floor_id' => $room->floor_id,
            'transaction_type_id' => 2,
            'assigned_frontdesk_id' => $assignedFrontdesk,
            'description' => 'Deposit',
            'payable_amount' => 0,
            'paid_amount' => 0,
            'change_amount' => 0,
            'deposit_amount' => 0,
            'paid_at' => now(),
            'remarks' => 'Deposit From Check In (Room Key & TV Remote)',
            'is_co' => true,
            'shift' => $shift,
        ]);

        // New guest report
        $now = Carbon::now();
        $shiftDate = ($now->hour >= 8 && $now->hour < 20)
            ? $now->format('F j, Y')
            : ($now->hour < 8 ? $now->copy()->subDay()->format('F j, Y') : $now->format('F j, Y'));

        NewGuestReport::create([
            'branch_id' => $branchId,
            'checkin_details_id' => $checkin->id,
            'room_id' => $data['room_id'],
            'shift_date' => $shiftDate,
            'shift' => $shift,
            'frontdesk_id' => 1,
            'partner_name' => 'Admin',
        ]);

        $room->update(['status' => 'Occupied']);

        ActivityLog::create([
            'branch_id' => $branchId,
            'user_id' => auth()->id(),
            'activity' => 'Check-In C/O',
            'description' => 'C/O guest ' . $data['name'] . ' checked in to Room #' . $room->number,
        ]);

        DB::commit();

        $this->dialog()->success('Check-In Complete', 'C/O guest has been checked in successfully.');
    }

    public function render()
    {
        return view('livewire.admin.manage.reservation');
    }
}
