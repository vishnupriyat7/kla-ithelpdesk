<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintRegisterResource\Pages;
use App\Filament\Resources\ComplaintRegisterResource\RelationManagers;
use App\Models\ComplaintRegister;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;

class ComplaintRegisterResource extends Resource
{
    protected static ?string $model = ComplaintRegister::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Complaint Register';
    protected static ?string $modelLabel = 'Complaint Register';
    protected static ?int $navigationSort = 1;

    protected static $employeeCache = null;

    public static function getEmployees()
    {
        if (static::$employeeCache === null) {
            try {
                $response = Http::get(env('EMPLOYEE_API_URL'));
                if ($response->successful()) {
                    static::$employeeCache = collect($response->json());
                } else {
                    static::$employeeCache = collect([]);
                }
            } catch (\Exception $e) {
                static::$employeeCache = collect([]);
            }
        }
        return static::$employeeCache;
    }

    public static function resolveEmployeeName($employeeId)
    {
        if (!$employeeId)
            return '-';

        // If it's a name (contains letters), return as is
        if (preg_match('/[a-zA-Z]/', $employeeId))
            return $employeeId;

        static::getEmployees();

        $employee = static::$employeeCache->first(function ($emp) use ($employeeId) {
            return (string) ($emp['pen'] ?? '') === (string) $employeeId ||
                (string) ($emp['attendanceId'] ?? '') === (string) $employeeId;
        });

        return $employee ? $employee['name'] : $employeeId;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_no')
                            ->disabled()
                            ->hiddenOn('create')
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('status')
                            ->disabled()
                            ->hiddenOn('create')
                            ->dehydrated(false),
                        Forms\Components\Select::make('section')
                            ->label('Section')
                            ->options(function (Forms\Get $get) {
                                $employees = static::getEmployees();
                                $sections = $employees->pluck('section')->filter()->unique()->values()->toArray();
                                $customSections = [
                                    "Minister's Room",
                                    "Residence of Deputy Speaker",
                                    "Residence of Secretary",
                                    "Residence of Speaker"
                                ];
                                $allSections = array_unique(array_merge($sections, $customSections));
                                sort($allSections);
                                
                                $options = array_combine($allSections, $allSections);
                                $current = $get('section');
                                if ($current && !isset($options[$current])) {
                                    $options[$current] = $current;
                                }
                                return $options;
                            })
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                $employees = static::getEmployees();
                                $sections = $employees->pluck('section')->filter()->unique()->values()->toArray();
                                $customSections = [
                                    "Minister's Room",
                                    "Residence of Deputy Speaker",
                                    "Residence of Secretary",
                                    "Residence of Speaker"
                                ];
                                $allSections = array_unique(array_merge($sections, $customSections));
                                sort($allSections);
                                
                                $filtered = collect($allSections)->filter(fn($sec) => stripos($sec, $search) !== false)->take(50);
                                $results = $filtered->mapWithKeys(fn($s) => [$s => $s])->toArray();
                                
                                if (trim($search) !== '' && !isset($results[$search])) {
                                    $results[$search] = $search . ' (Add New)';
                                }
                                
                                return $results;
                            })
                            ->getOptionLabelUsing(fn ($value): ?string => str_replace(' (Add New)', '', $value))
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                // If they selected the "Add New" option string, strip the "(Add New)" part
                                if (str_ends_with($state, ' (Add New)')) {
                                    $set('section', str_replace(' (Add New)', '', $state));
                                }
                                $set('employee_id', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->options(function (Forms\Get $get) {
                                $section = $get('section');
                                if (! $section) {
                                    return [];
                                }
                                
                                $options = static::getEmployees()
                                    ->where('section', $section)
                                    ->mapWithKeys(function ($emp) {
                                        return [$emp['pen'] => $emp['name'] . ' (' . $emp['pen'] . ')'];
                                    })->toArray();
                                    
                                $current = $get('employee_id');
                                if ($current && !isset($options[$current])) {
                                    $options[$current] = $current;
                                }
                                
                                return $options;
                            })
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search, Forms\Get $get) {
                                $section = $get('section');
                                if (! $section) {
                                    return [];
                                }
                                
                                $employees = static::getEmployees()
                                    ->where('section', $section)
                                    ->mapWithKeys(function ($emp) {
                                        return [$emp['pen'] => $emp['name'] . ' (' . $emp['pen'] . ')'];
                                    });
                                
                                $filtered = $employees->filter(fn($name, $pen) => stripos($name, $search) !== false || stripos($pen, $search) !== false)->take(50);
                                $results = $filtered->toArray();
                                
                                if (trim($search) !== '' && !in_array($search, $results) && !isset($results[$search])) {
                                    $results[$search] = $search . ' (Add New)'; 
                                }
                                
                                return $results;
                            })
                            ->getOptionLabelUsing(function ($value, Forms\Get $get) {
                                $section = $get('section');
                                if (! $section) return str_replace(' (Add New)', '', $value);
                                
                                $emp = static::getEmployees()->firstWhere('pen', $value);
                                if ($emp) {
                                    return $emp['name'] . ' (' . $emp['pen'] . ')';
                                }
                                return str_replace(' (Add New)', '', $value);
                            })
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if (str_ends_with($state, ' (Add New)')) {
                                    $set('employee_id', str_replace(' (Add New)', '', $state));
                                }
                            }),
                        Forms\Components\Select::make('complaint_type')
                            ->options([
                                'Hardware' => 'Hardware',
                                'Software' => 'Software',
                                'Network' => 'Network',
                                'Printer' => 'Printer',
                                'Email' => 'Email',
                            ])
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Location Details')
                    ->schema([
                        Forms\Components\Select::make('office_location_id')
                            ->relationship('location', 'location')
                            ->label('Building')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('floor', null);
                                $set('room_id', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('floor')
                            ->label('Floor')
                            ->options(function (Forms\Get $get) {
                                $locationId = $get('office_location_id');
                                if (! $locationId) {
                                    return [];
                                }
                                $roomFloors = \App\Models\Room::where('office_location_id', $locationId)
                                    ->whereNotNull('floor')
                                    ->pluck('floor');
                                $floors = \App\Models\Floor::whereIn('name', $roomFloors)
                                    ->orderBy('sort_order')
                                    ->pluck('name');
                                $missing = $roomFloors->diff($floors);
                                $floors = $floors->concat($missing)->unique();
                                return $floors->mapWithKeys(fn ($f) => [$f => $f])->toArray();
                            })
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('room_id', null))
                            ->searchable(),
                        Forms\Components\Select::make('room_id')
                            ->label('Room')
                            ->options(function (Forms\Get $get) {
                                $locationId = $get('office_location_id');
                                $floor = $get('floor');
                                if (! $locationId || ! $floor) {
                                    return [];
                                }
                                return \App\Models\Room::where('office_location_id', $locationId)
                                    ->where('floor', $floor)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable(),
                    ])->columns(3),
                Forms\Components\Section::make('Problem & Resolution')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'name', fn (Builder $query) => $query->whereHas('role', fn($q) => $q->whereIn('name', ['chm', 'programmer'])))
                            ->label('Assigned Technician')
                            ->disabled(fn () => !in_array(auth()->user()->getRoleName(), ['admin', 'superadmin', 'hardwareadmin'])),
                        Forms\Components\Textarea::make('remarks')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Ticket & Location Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('ticket_no')
                                    ->label('Ticket #')
                                    ->weight('bold')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'Open' => 'danger',
                                        'Assigned' => 'warning',
                                        'Pending' => 'warning',
                                        'Complaint' => 'danger',
                                        'Resolved' => 'success',
                                        'Closed' => 'gray',
                                        default => 'secondary',
                                    }),
                                Infolists\Components\TextEntry::make('vendor_complaint_id')
                                    ->label('Complaint ID')
                                    ->badge()
                                    ->color('danger')
                                    ->icon('heroicon-m-exclamation-triangle')
                                    ->visible(fn($state) => filled($state)),
                                Infolists\Components\TextEntry::make('employee_id')
                                    ->label('Employee')
                                    ->formatStateUsing(fn($state) => static::resolveEmployeeName($state)),
                                Infolists\Components\TextEntry::make('section'),
                                Infolists\Components\TextEntry::make('complaint_type')
                                    ->label('Category'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Raised At')
                                    ->dateTime('d-M-Y h:i A')
                                    ->timezone('Asia/Kolkata'),
                                Infolists\Components\TextEntry::make('location_details')
                                    ->label('Location (Bldg/Floor/Room)')
                                    ->getStateUsing(
                                        fn(ComplaintRegister $record): string =>
                                        ($record->location?->location ?? '-') . ' / ' .
                                        ($record->floor ?? '-') . ' / ' .
                                        ($record->room?->name ?? '-')
                                    )
                                    ->icon('heroicon-m-map-pin')
                                    ->color('primary')
                                    ->columnSpan(1),
                                Infolists\Components\TextEntry::make('description')
                                    ->label('Problem Description')
                                    ->columnSpan(2)
                                    ->prose()
                                    ->markdown()
                                    ->icon('heroicon-m-chat-bubble-bottom-center-text'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Resolution Tracking')
                    ->schema([
                        Infolists\Components\ViewEntry::make('status_timeline')
                            ->hiddenLabel()
                            ->view('filament.complaintregister.timeline')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('technician.name')
                            ->label('Assigned Technician')
                            ->placeholder('Unassigned'),
                        Infolists\Components\TextEntry::make('vendor_complaint_id')
                            ->label('Complaint ID')
                            ->badge()
                            ->color('danger')
                            ->icon('heroicon-m-exclamation-triangle')
                            ->visible(fn($state) => filled($state)),
                        Infolists\Components\TextEntry::make('remarks')
                            ->label('Final Resolution Remarks')
                            ->placeholder('No remarks provided')
                            ->columnSpanFull(),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_no')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Open' => 'danger',
                        'Assigned' => 'warning',
                        'Pending' => 'warning',
                        'Complaint' => 'danger',
                        'Resolved' => 'success',
                        'Closed' => 'gray',
                        default => 'secondary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendor_complaint_id')
                    ->label('Complaint ID')
                    ->badge()
                    ->color('danger')
                    ->url(function ($record) {
                        if (!$record->vendor_complaint_id) {
                            return null;
                        }
                        $complaint = \App\Models\VendorComplaint::where('vendor_complaint_no', $record->vendor_complaint_id)->first();
                        if ($complaint) {
                            return \App\Filament\Resources\VendorComplaintResource::getUrl('edit', ['record' => $complaint->id]);
                        }
                        return \App\Filament\Resources\VendorComplaintResource::getUrl('index');
                    })
                    ->openUrlInNewTab()
                    ->searchable(),

                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Requested By')
                    ->formatStateUsing(fn($state) => static::resolveEmployeeName($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('section')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.location')
                    ->label('Building')
                    ->sortable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Room')
                    ->sortable(),
                Tables\Columns\TextColumn::make('complaint_type')
                    ->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('technician.name')
                    ->label('Technician')
                    ->placeholder('Unassigned')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Raised At')
                    ->dateTime('d-M-Y h:i A')
                    ->timezone('Asia/Kolkata')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Open' => 'Open',
                        'Assigned' => 'Assigned',
                        'Pending' => 'Pending',
                        'Complaint' => 'Complaint',
                        'Resolved' => 'Resolved',
                    ]),
                Tables\Filters\SelectFilter::make('complaint_type')
                    ->options([
                        'Hardware' => 'Hardware',
                        'Software' => 'Software',
                        'Network' => 'Network',
                        'Printer' => 'Printer',
                        'Email' => 'Email',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->hiddenLabel(),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil')
                    ->color('warning')
                    ->hiddenLabel(),
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->hiddenLabel()
                    ->url(function ($record) {
                        $employeeName = static::resolveEmployeeName($record->employee_id);
                        $location = ($record->location?->location ?? '-') . ' / ' . ($record->floor ?? '-') . ' / ' . ($record->room?->name ?? '-');
                        $userName = auth()->user()->name;
                        $message = "*{$userName}*\n\n";
                        $message .= "*Ticket No:* {$record->ticket_no}";
                        
                        if ($employeeName && $employeeName !== '-' && $employeeName !== 'Not Provided') {
                            $message .= "\n*Requested By:* {$employeeName}";
                        }
                        if ($record->section && $record->section !== '-') {
                            $message .= "\n*Section:* {$record->section}";
                        }
                        
                        $locParts = [];
                        if ($record->location && $record->location->location && $record->location->location !== '-') $locParts[] = $record->location->location;
                        if ($record->floor && $record->floor !== '-') $locParts[] = $record->floor;
                        if ($record->room && $record->room->name && $record->room->name !== '-') $locParts[] = $record->room->name;
                        
                        $locStr = implode(' / ', $locParts);
                        if ($locStr) {
                            $message .= "\n*Location:* {$locStr}";
                        }
                        
                        if ($record->complaint_type && $record->complaint_type !== '-') {
                            $message .= "\n*Type:* {$record->complaint_type}";
                        }
                        if ($record->description && $record->description !== '-') {
                            $message .= "\n*Problem:* {$record->description}";
                        }
                        
                        $displayStatus = $record->status;
                        if ($record->status === 'Complaint' && $record->vendor_complaint_id) {
                            $displayStatus = "Complaint (No.{$record->vendor_complaint_id})";
                        }
                        $message .= "\n*Status:* {$displayStatus}";
                        
                        if ($record->technician) {
                            $message .= "\n*Handled By:* {$record->technician->name}";
                        }
                        
                        return 'https://wa.me/?text=' . urlencode($message);
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        $role = auth()->user()->getRoleName();
        return auth()->check() && in_array($role, ['admin', 'hardwareadmin', 'superadmin']);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $role = auth()->user()->getRoleName();
        if (in_array($role, ['admin', 'superadmin', 'hardwareadmin'])) {
            return true;
        }
        return $record->technician_id === auth()->id();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StatusHistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaintRegisters::route('/'),
            'create' => Pages\CreateComplaintRegister::route('/create'),
            'view' => Pages\ViewComplaintRegister::route('/{record}'),
            'edit' => Pages\EditComplaintRegister::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $role = auth()->user()->getRoleName();

        // Admin, Superadmin, and Hardwareadmin can see all tickets. Others (like chm, programmer) only see their assigned tickets.
        if (!in_array($role, ['admin', 'superadmin', 'hardwareadmin'])) {
            $query->where('technician_id', auth()->id());
        }

        return $query;
    }
}
