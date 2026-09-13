<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsappImportStagingResource\Pages;
use App\Models\ComplaintRegister;
use App\Models\ComplaintType;
use App\Models\OfficeLocation;
use App\Models\Room;
use App\Models\Section;
use App\Models\User;
use App\Models\WhatsappImportStaging;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WhatsappImportStagingResource extends Resource
{
    protected static ?string $model = WhatsappImportStaging::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Complaint Register';
    protected static ?string $navigationLabel = 'WhatsApp Staging';
    protected static ?string $modelLabel = 'Staged Complaint';
    protected static ?string $pluralModelLabel = 'Staged Complaints';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $role = auth()->user()?->getRoleName();
        return auth()->check() && in_array($role, ['superadmin', 'hardwareadmin', 'admin', 'chm', 'cowd', 'programmer']);
    }

    public static function canCreate(): bool
    {
        return false; // Created via chat sync/importer
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $role = auth()->user()?->getRoleName();
        return auth()->check() && in_array($role, ['superadmin', 'hardwareadmin', 'admin', 'chm', 'cowd']);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $role = auth()->user()?->getRoleName();
        return auth()->check() && in_array($role, ['superadmin', 'hardwareadmin', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Complaint Details')
                    ->description('Review and edit the complaint information before importing into tickets.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DateTimePicker::make('reported_at')
                                    ->label('Reported Date & Time')
                                    ->required(),
                                Forms\Components\TextInput::make('ticket_no')
                                    ->label('Ticket #')
                                    ->placeholder('Auto-generated on import if empty')
                                    ->helperText('Existing IT-XXXX ticket if mentioned in chat'),
                                Forms\Components\Select::make('section')
                                    ->label('Section')
                                    ->options(fn () => Section::pluck('name', 'name')->toArray())
                                    ->searchable()
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('office_location_id')
                                    ->label('Location')
                                    ->relationship('location', 'location')
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                Forms\Components\TextInput::make('floor')
                                    ->label('Floor / Specific Area'),
                                Forms\Components\Select::make('room_id')
                                    ->label('Room')
                                    ->relationship('room', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('complaint_type')
                                    ->label('Complaint Type')
                                    ->options(fn () => ComplaintType::pluck('name', 'name')->toArray())
                                    ->required(),
                                Forms\Components\TextInput::make('employee_id')
                                    ->label('Requested By / Staff ID'),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Complaint Description')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('CHM Technician & Status')
                    ->description('Assign the CHM technician and confirm status before ticket insertion.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Open' => 'Open',
                                        'Assigned' => 'Assigned',
                                        'Pending' => 'Pending',
                                        'Complaint' => 'Complaint',
                                        'Resolved' => 'Resolved',
                                        'Closed' => 'Closed',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('technician_id')
                                    ->label('Assigned CHM (Technician)')
                                    ->options(fn () => User::whereHas('role', fn ($q) => $q->whereIn('name', ['chm', 'programmer', 'hardwareadmin']))->pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('vendor_complaint_id')
                                    ->label('AMC / Vendor Complaint ID')
                                    ->placeholder('e.g. 18344, 18413'),
                            ]),
                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks / Resolution Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('WhatsApp Source Trail')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('merge_count')
                                    ->label('Merged Messages Count')
                                    ->disabled(),
                                Forms\Components\Toggle::make('already_imported')
                                    ->label('Already Imported to Tickets')
                                    ->disabled(),
                            ]),
                        Forms\Components\Textarea::make('raw_messages')
                            ->label('Full WhatsApp Chat History for this Complaint')
                            ->rows(6)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reported_at')
                    ->label('Reported')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ticket_no')
                    ->label('Ticket #')
                    ->badge()
                    ->color('primary')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('section')
                    ->label('Section')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('location.location')
                    ->label('Location')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('floor')
                    ->label('Floor')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Room')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('complaint_type')
                    ->label('Type')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(45)
                    ->tooltip(fn ($record) => $record->description)
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'Open',
                        'info' => 'Assigned',
                        'gray' => 'Pending',
                        'danger' => 'Complaint',
                        'success' => 'Resolved',
                    ])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('technician.name')
                    ->label('Assigned CHM')
                    ->placeholder('Unassigned')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('vendor_complaint_id')
                    ->label('AMC ID')
                    ->badge()
                    ->color('danger')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(35)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('merge_count')
                    ->label('Msgs')
                    ->badge()
                    ->color(fn ($state) => $state > 1 ? 'success' : 'gray')
                    ->tooltip(fn ($record) => "{$record->merge_count} messages consolidated")
                    ->toggleable(),
                Tables\Columns\IconColumn::make('already_imported')
                    ->label('Imported')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('imported_at')
                    ->label('Imported At')
                    ->dateTime('d/m/Y h:i A')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Open' => 'Open',
                        'Assigned' => 'Assigned',
                        'Pending' => 'Pending',
                        'Complaint' => 'Complaint',
                        'Resolved' => 'Resolved',
                        'Closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('technician_id')
                    ->label('CHM Technician')
                    ->options(fn () => User::whereHas('role', fn ($q) => $q->whereIn('name', ['chm', 'programmer', 'hardwareadmin']))->pluck('name', 'id')->toArray())
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('already_imported')
                    ->label('Import Status')
                    ->placeholder('Pending Import Only')
                    ->trueLabel('Imported Only')
                    ->falseLabel('Pending Import Only')
                    ->default(false),
            ])
            ->actions([
                // Quick Action: Select CHM & Status
                Tables\Actions\Action::make('select_chm_status')
                    ->label('Select CHM / Status')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('technician_id')
                            ->label('Select CHM (Technician)')
                            ->options(fn () => User::whereHas('role', fn ($q) => $q->whereIn('name', ['chm', 'programmer', 'hardwareadmin']))->pluck('name', 'id')->toArray())
                            ->default(fn ($record) => $record->technician_id)
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->label('Select Status')
                            ->options([
                                'Open' => 'Open',
                                'Assigned' => 'Assigned',
                                'Pending' => 'Pending',
                                'Complaint' => 'Complaint',
                                'Resolved' => 'Resolved',
                                'Closed' => 'Closed',
                            ])
                            ->default(fn ($record) => $record->status)
                            ->required(),
                        Forms\Components\TextInput::make('vendor_complaint_id')
                            ->label('AMC / Vendor Complaint ID')
                            ->default(fn ($record) => $record->vendor_complaint_id),
                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->default(fn ($record) => $record->remarks),
                    ])
                    ->action(function (WhatsappImportStaging $record, array $data) {
                        $record->update([
                            'technician_id' => $data['technician_id'] ?? null,
                            'status' => $data['status'],
                            'vendor_complaint_id' => $data['vendor_complaint_id'] ?? null,
                            'remarks' => $data['remarks'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Complaint Updated')
                            ->success()
                            ->send();
                    }),

                // View WhatsApp Chat Transcript
                Tables\Actions\Action::make('view_chat')
                    ->label('Chat')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->color('gray')
                    ->modalHeading('WhatsApp Message History')
                    ->modalDescription('The full chain of messages merged into this single ticket.')
                    ->modalContent(fn ($record) => view('filament.components.raw-chat-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Tables\Actions\EditAction::make(),

                // SINGLE ROW IMPORT ACTION: ONLY superadmin and hardwareadmin can import!
                Tables\Actions\Action::make('import_to_tickets')
                    ->label('Import')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Import into Complaint Tickets')
                    ->modalDescription('This will insert this complaint as ONE ticket into the official Complaint Register. Continue?')
                    ->visible(function ($record) {
                        $role = auth()->user()?->getRoleName();
                        return !$record->already_imported && in_array($role, ['superadmin', 'hardwareadmin']);
                    })
                    ->action(function (WhatsappImportStaging $record) {
                        $role = auth()->user()?->getRoleName();
                        if (!in_array($role, ['superadmin', 'hardwareadmin'])) {
                            Notification::make()
                                ->title('Unauthorized')
                                ->body('Only Super Admin and Hardware Admin can import complaints.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $ticket = static::importSingleRecord($record);

                        Notification::make()
                            ->title('Ticket Imported Successfully')
                            ->body("Ticket #{$ticket->ticket_no} created in Complaint Register.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => in_array(auth()->user()?->getRoleName(), ['superadmin', 'hardwareadmin', 'admin'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // BULK IMPORT ACTION: ONLY superadmin and hardwareadmin can import!
                    Tables\Actions\BulkAction::make('bulk_import_to_tickets')
                        ->label('Import Selected to Tickets')
                        ->icon('heroicon-o-arrow-down-on-square-stack')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Import Selected into Complaint Register')
                        ->modalDescription('Are you sure you want to import all selected complaints? Each will create exactly ONE official ticket.')
                        ->visible(fn () => in_array(auth()->user()?->getRoleName(), ['superadmin', 'hardwareadmin']))
                        ->action(function (Collection $records) {
                            $role = auth()->user()?->getRoleName();
                            if (!in_array($role, ['superadmin', 'hardwareadmin'])) {
                                Notification::make()
                                    ->title('Unauthorized')
                                    ->body('Only Super Admin and Hardware Admin can import complaints.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $importedCount = 0;
                            DB::transaction(function () use ($records, &$importedCount) {
                                foreach ($records as $record) {
                                    if ($record->already_imported) {
                                        continue;
                                    }
                                    static::importSingleRecord($record);
                                    $importedCount++;
                                }
                            });

                            Notification::make()
                                ->title('Import Completed')
                                ->body("{$importedCount} complaints successfully imported into Complaint Register.")
                                ->success()
                                ->send();
                        }),

                    // Bulk Assign CHM
                    Tables\Actions\BulkAction::make('bulk_assign_chm')
                        ->label('Assign CHM (Technician)')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('technician_id')
                                ->label('Select CHM')
                                ->options(fn () => User::whereHas('role', fn ($q) => $q->whereIn('name', ['chm', 'programmer', 'hardwareadmin']))->pluck('name', 'id')->toArray())
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['technician_id' => $data['technician_id']]);
                            Notification::make()
                                ->title('CHM Assigned to Selected Complaints')
                                ->success()
                                ->send();
                        }),

                    // Bulk Set Status
                    Tables\Actions\BulkAction::make('bulk_set_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Select Status')
                                ->options([
                                    'Open' => 'Open',
                                    'Assigned' => 'Assigned',
                                    'Pending' => 'Pending',
                                    'Complaint' => 'Complaint',
                                    'Resolved' => 'Resolved',
                                    'Closed' => 'Closed',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['status' => $data['status']]);
                            Notification::make()
                                ->title('Status Updated for Selected Complaints')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => in_array(auth()->user()?->getRoleName(), ['superadmin', 'hardwareadmin', 'admin'])),
                ]),
            ])
            ->defaultSort('reported_at', 'asc');
    }

    /**
     * Import a single staging record into complaint_tickets table.
     */
    public static function importSingleRecord(WhatsappImportStaging $record): ComplaintRegister
    {
        return DB::transaction(function () use ($record) {
            $reportedDate = $record->reported_at ?? now();

            $ticket = ComplaintRegister::create([
                'ticket_no' => $record->ticket_no, // If null, auto-generated by ComplaintRegister boot
                'employee_id' => $record->employee_id,
                'section' => $record->section ?: 'IT Section',
                'office_location_id' => $record->office_location_id,
                'floor' => $record->floor,
                'room_id' => $record->room_id,
                'complaint_type' => $record->complaint_type ?: 'Computer',
                'description' => $record->description ?: 'WhatsApp Complaint',
                'status' => $record->status ?: 'Open',
                'technician_id' => $record->technician_id,
                'remarks' => $record->remarks,
                'vendor_complaint_id' => $record->vendor_complaint_id,
                'user_id' => $record->user_id ?: auth()->id(),
                'created_at' => $reportedDate,
                'custom_status_date' => $reportedDate,
            ]);

            $record->update([
                'already_imported' => true,
                'imported_ticket_id' => $ticket->id,
                'imported_at' => now(),
                'ticket_no' => $ticket->ticket_no,
            ]);

            return $ticket;
        });
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWhatsappImportStagings::route('/'),
            'edit' => Pages\EditWhatsappImportStaging::route('/{record}/edit'),
        ];
    }
}
