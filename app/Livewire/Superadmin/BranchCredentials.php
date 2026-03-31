<?php

namespace App\Livewire\Superadmin;

use Filament\Tables;
use App\Models\Branch;
use App\Models\User;
use App\Models\ActivityLog;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;

class BranchCredentials extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $branchId;
    public $branchName;

    public function mount($id)
    {
        $branch = Branch::findOrFail($id);
        $this->branchId = $branch->id;
        $this->branchName = $branch->name;
    }

    protected function getTableQuery(): Builder
    {
        return User::where('branch_id', $this->branchId);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('NAME')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('email')
                ->label('EMAIL')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('roles.name')
                ->label('ROLE')
                ->badge()
                ->formatStateUsing(fn(string $state): string => strtoupper($state))
                ->color('success'),
            Tables\Columns\ToggleColumn::make('is_active')
                ->label('ACTIVE')
                ->onColor('success')
                ->offColor('danger'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->form($this->getUserFormSchema(isEdit: true))
                ->color('success')
                ->button()
                ->mutateFormDataUsing(function (array $data): array {
                    if (!empty($data['password'])) {
                        $data['password'] = Hash::make($data['password']);
                    } else {
                        unset($data['password']);
                    }
                    return $data;
                })
                ->after(function ($record) {
                    if (isset($record->getChanges()['password']) || request()->has('role')) {
                        // Role is handled separately
                    }
                    ActivityLog::create([
                        'branch_id' => $this->branchId,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Update User',
                        'description' => 'Updated user ' . $record->name,
                    ]);
                }),
            Tables\Actions\DeleteAction::make()
                ->button(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('save')
                ->disableCreateAnother()
                ->modalHeading('Add New User')
                ->modalButton('Save')
                ->label('Add New')
                ->button()
                ->color('info')
                ->icon('heroicon-o-plus')
                ->form($this->getUserFormSchema(isEdit: false))
                ->action(function (array $data) {
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'branch_id' => $this->branchId,
                        'branch_name' => $this->branchName,
                    ]);

                    $user->assignRole($data['role']);

                    ActivityLog::create([
                        'branch_id' => $this->branchId,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Create User',
                        'description' => 'Created user ' . $data['name'],
                    ]);

                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'User created successfully'
                    );
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getUserFormSchema(bool $isEdit): array
    {
        return [
            TextInput::make('name')
                ->label('Name')
                ->required(),
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required(),
            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(!$isEdit)
                ->dehydrated(fn ($state) => filled($state))
                ->hint($isEdit ? 'Leave blank to keep current password' : ''),
            Select::make('role')
                ->label('Role')
                ->options([
                    'superadmin' => 'Superadmin',
                    'admin' => 'Admin',
                    'frontdesk' => 'Frontdesk',
                    'kiosk' => 'Kiosk',
                    'kitchen' => 'Kitchen',
                    'back_office' => 'Back Office',
                    'roomboy' => 'Roomboy',
                    'pub_kitchen' => 'Pub Kitchen',
                ])
                ->required(),
        ];
    }

    public function render()
    {
        return view('livewire.superadmin.branch-credentials');
    }
}
