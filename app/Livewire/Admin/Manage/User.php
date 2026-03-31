<?php

namespace App\Livewire\Admin\Manage;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Frontdesk;
use App\Models\User as UserModel;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class User extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $type = 1;

    private function getBranchId(): int
    {
        return auth()->user()->branch_id;
    }

    protected function getTableQuery(): Builder
    {
        $query = UserModel::query()
            ->whereHas('roles', fn ($q) => $q->whereNotIn('name', ['superadmin']))
            ->with('roles');

        if (!auth()->user()->hasRole('superadmin')) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('branch.name')
                ->label('BRANCH')
                ->formatStateUsing(fn (string $state): string => strtoupper($state))
                ->sortable()
                ->visible(fn () => auth()->user()->hasRole('superadmin')),
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
                ->formatStateUsing(function ($record) {
                    $role = $record->roles->first()?->name;
                    return $role ? strtoupper(str_replace('_', ' ', $role)) : 'NO ROLE';
                })
                ->badge()
                ->color('info')
                ->searchable()
                ->sortable(),
            Tables\Columns\ToggleColumn::make('is_active')
                ->label('ACTIVE')
                ->onColor('success')
                ->offColor('danger')
                ->disabled(fn ($record) => !auth()->user()->hasRole('superadmin') && $record->hasRole('admin')),
            Tables\Columns\TextColumn::make('online')
                ->label('STATUS')
                ->badge()
                ->getStateUsing(function ($record) {
                    $threshold = now()->subMinutes(5)->timestamp;
                    return $record->sessions()
                        ->where('last_activity', '>=', $threshold)
                        ->exists() ? 'yes' : 'no';
                })
                ->formatStateUsing(fn ($state) => $state === 'yes' ? 'Online' : 'Offline')
                ->icon(fn ($state) => $state === 'yes' ? 'heroicon-o-link' : 'heroicon-o-x-circle')
                ->color(fn ($state) => $state === 'yes' ? 'success' : 'danger'),
        ];
    }

    protected function getTablePollingInterval(): ?string
    {
        return '5s';
    }

    protected function getTableHeaderActions(): array
    {
        $isSuperadmin = auth()->user()->hasRole('superadmin');

        return [
            CreateAction::make('add_user')
                ->label('Add New User')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->button()
                ->disableCreateAnother()
                ->modalHeading('Add New User')
                ->form(function () use ($isSuperadmin) {
                    $fields = [];

                    if ($isSuperadmin) {
                        $fields[] = Select::make('branch_id')
                            ->label('Branch')
                            ->options(Branch::pluck('name', 'id'))
                            ->required()
                            ->columnSpanFull();
                    }

                    $fields = array_merge($fields, [
                        TextInput::make('name')->required(),
                        TextInput::make('email')->email()->required(),
                        TextInput::make('password')->password()->required(),
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'frontdesk' => 'Frontdesk',
                                'kiosk' => 'Kiosk',
                                'kitchen' => 'Kitchen',
                                'pub_kitchen' => 'Pub Kitchen',
                                'roomboy' => 'Roomboy',
                                'back_office' => 'Back Office',
                            ])
                            ->required(),
                    ]);

                    return [Grid::make(2)->schema($fields)];
                })
                ->action(function (array $data) use ($isSuperadmin) {
                    $branchId = $isSuperadmin
                        ? $data['branch_id']
                        : auth()->user()->branch_id;

                    $user = UserModel::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => bcrypt($data['password']),
                        'branch_id' => $branchId,
                        'branch_name' => Branch::find($branchId)?->name,
                    ]);

                    $user->assignRole($data['role']);

                    if ($data['role'] === 'frontdesk') {
                        Frontdesk::create([
                            'branch_id' => $branchId,
                            'user_id' => $user->id,
                            'name' => $data['name'],
                            'number' => '+639000000000',
                        ]);
                    }

                    ActivityLog::create([
                        'branch_id' => $branchId,
                        'user_id' => auth()->id(),
                        'activity' => 'Create User',
                        'description' => 'Created user ' . $data['name'],
                    ]);

                    $this->dialog()->success('User Created', 'The user has been created successfully.');
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->color('success')
                ->button()
                ->size('sm')
                ->action(function ($record, $data) {
                    $oldRole = $record->roles->first()?->name;

                    if ($oldRole && $data['role'] !== $oldRole) {
                        $record->removeRole($oldRole);
                        $record->assignRole($data['role']);
                    } elseif (!$oldRole) {
                        $record->assignRole($data['role']);
                    }

                    $updateData = [
                        'name' => $data['name'],
                        'email' => $data['email'],
                    ];

                    if (!empty($data['password'])) {
                        $updateData['password'] = bcrypt($data['password']);
                    }

                    $record->update($updateData);

                    ActivityLog::create([
                        'branch_id' => $record->branch_id,
                        'user_id' => auth()->id(),
                        'activity' => 'Update User',
                        'description' => 'Updated user ' . $data['name'],
                    ]);

                    $this->dialog()->success('User Updated', 'The user has been updated successfully.');
                })
                ->form(function ($record) {
                    return [
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->default($record->name)
                                ->required(),
                            TextInput::make('email')
                                ->default($record->email)
                                ->email()
                                ->required(),
                            TextInput::make('password')
                                ->password()
                                ->placeholder('Leave blank to keep current')
                                ->dehydrated(fn ($state) => filled($state)),
                            Select::make('role')
                                ->options([
                                    'admin' => 'Admin',
                                    'frontdesk' => 'Frontdesk',
                                    'kiosk' => 'Kiosk',
                                    'kitchen' => 'Kitchen',
                                    'pub_kitchen' => 'Pub Kitchen',
                                    'roomboy' => 'Roomboy',
                                    'back_office' => 'Back Office',
                                ])
                                ->required()
                                ->default($record->roles->first()?->name),
                        ]),
                    ];
                })
                ->modalHeading('Update User')
                ->modalWidth('xl'),
        ];
    }

    protected function getTableFilters(): array
    {
        if (auth()->user()->hasRole('superadmin')) {
            return [
                SelectFilter::make('branch')->relationship('branch', 'name'),
            ];
        }

        return [];
    }

    protected function getTableFiltersLayout(): ?string
    {
        return FiltersLayout::AboveContent->value;
    }

    public function render()
    {
        return view('livewire.admin.manage.user');
    }
}
