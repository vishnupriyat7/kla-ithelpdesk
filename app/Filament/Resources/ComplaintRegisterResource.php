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
                        Forms\Components\Select::make('status')
                            ->options([
                                'Open' => 'Open',
                                'Assigned' => 'Assigned',
                                'Pending' => 'Pending',
                                'Complaint' => 'Complaint',
                                'Resolved' => 'Resolved',
                                'Closed' => 'Closed',
                            ])
                            ->disabled(fn () => !in_array(auth()->user()->getRoleName(), ['admin', 'superadmin', 'hardwareadmin']))
                            ->hiddenOn('create')
                            ->dehydrated(fn () => in_array(auth()->user()->getRoleName(), ['admin', 'superadmin', 'hardwareadmin'])),
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
                                'Computer' => 'Computer',
                                'Software' => 'Software',
                                'Network' => 'Network',
                                'Printer' => 'Printer',
                                'Email' => 'Email',
                                'E-Office' => 'E-Office',
                            ])
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Location Details')
                    ->schema([
                        Forms\Components\Select::make('office_location_id')
                            ->label('Building')
                            ->options(fn() => \App\Models\OfficeLocation::pluck('location', 'id'))
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
                                $roomFloorIds = \App\Models\Room::where('office_location_id', $locationId)
                                    ->whereNotNull('floor_id')
                                    ->pluck('floor_id');
                                $floors = \App\Models\Floor::whereIn('id', $roomFloorIds)
                                    ->orderBy('sort_order')
                                    ->pluck('name', 'name');
                                return $floors->toArray();
                            })
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('room_id', null))
                            ->searchable(),
                        Forms\Components\Select::make('room_id')
                            ->label('Room')
                            ->options(function (Forms\Get $get) {
                                $locationId = $get('office_location_id');
                                $floorName = $get('floor');
                                if (! $locationId || ! $floorName) {
                                    return [];
                                }
                                $floor = \App\Models\Floor::where('name', $floorName)->first();
                                if (!$floor) return [];

                                return \App\Models\Room::where('office_location_id', $locationId)
                                    ->where('floor_id', $floor->id)
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
                                    ->icon(fn (string $state): ?string => $state === 'Complaint' ? 'heroicon-m-exclamation-triangle' : null)
                                    ->url(function ($record) {
                                        if ($record->status === 'Complaint' && $record->vendor_complaint_id) {
                                            $vc = \App\Models\VendorComplaint::where('vendor_complaint_no', $record->vendor_complaint_id)->first();
                                            if ($vc) {
                                                return \App\Filament\Resources\VendorComplaintResource::getUrl('edit', ['record' => $vc->id]);
                                            }
                                        }
                                        return null;
                                    })
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
                                    ->url(function ($record) {
                                        if ($record->vendor_complaint_id) {
                                            $vc = \App\Models\VendorComplaint::where('vendor_complaint_no', $record->vendor_complaint_id)->first();
                                            if ($vc) {
                                                return \App\Filament\Resources\VendorComplaintResource::getUrl('edit', ['record' => $vc->id]);
                                            }
                                        }
                                        return null;
                                    })
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
                    ->icon(fn (string $state): ?string => $state === 'Complaint' ? 'heroicon-m-exclamation-triangle' : null)
                    ->url(function ($record) {
                        if ($record->status === 'Complaint' && $record->vendor_complaint_id) {
                            $vc = \App\Models\VendorComplaint::where('vendor_complaint_no', $record->vendor_complaint_id)->first();
                            if ($vc) {
                                return \App\Filament\Resources\VendorComplaintResource::getUrl('edit', ['record' => $vc->id]);
                            }
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
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
                    ->icon('heroicon-m-exclamation-triangle')
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
                        'Computer' => 'Computer',
                        'Software' => 'Software',
                        'Network' => 'Network',
                        'Printer' => 'Printer',
                        'Email' => 'Email',
                        'E-Office' => 'E-Office',
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
                    ->hiddenLabel()
                    ->tooltip('View Ticket'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil')
                    ->color('warning')
                    ->hiddenLabel()
                    ->tooltip('Edit Ticket'),
                Tables\Actions\Action::make('createVendorComplaint')
                    ->label('Log Vendor Complaint')
                    ->icon('heroicon-m-exclamation-triangle')
                    ->color('danger')
                    ->hiddenLabel()
                    ->tooltip('Create Vendor Complaint')
                    ->visible(fn (\App\Models\ComplaintRegister $record) => empty($record->vendor_complaint_id) && in_array(auth()->user()->getRoleName(), ['admin', 'superadmin', 'hardwareadmin', 'chm']))
                    ->form([
                        \Filament\Forms\Components\Select::make('vendor')
                            ->label('Vendor')
                            ->options([
                                'IHRD' => 'IHRD',
                                'Lipi' => 'Lipi',
                                'Aser' => 'Aser',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('vendor_complaint_no')
                            ->label('Vendor Complaint No')
                            ->unique('vendor_complaints', 'vendor_complaint_no')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('complaint_description')
                            ->label('Complaint Description')
                            ->required(),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'Un attended' => 'Un attended',
                                'Pending Spare' => 'Pending Spare',
                                'Resolved' => 'Resolved',
                            ])
                            ->default('Un attended')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('chm_remark')
                            ->label('Remarks')
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data, \App\Models\ComplaintRegister $record) {
                        \App\Models\VendorComplaint::create([
                            'complaint_ticket_id' => $record->id,
                            'vendor' => $data['vendor'],
                            'vendor_complaint_no' => $data['vendor_complaint_no'],
                            'complaint_description' => $data['complaint_description'],
                            'status' => $data['status'],
                            'chm_remark' => $data['chm_remark'] ?? null,
                        ]);
                        
                        $record->update([
                            'vendor_complaint_id' => $data['vendor_complaint_no'],
                            'status' => 'Complaint'
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Vendor Complaint Created Successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(new \Illuminate\Support\HtmlString('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="fi-ac-icon h-4 w-4 text-success-500"><path d="M12.031 0C5.385 0 0 5.386 0 12.032c0 2.12.553 4.195 1.603 6.012L.15 24l6.103-1.603a11.96 11.96 0 005.778 1.488h.005c6.645 0 12.03-5.386 12.03-12.032C24.066 5.386 18.679 0 12.031 0zm.005 21.895a9.98 9.98 0 01-5.086-1.39l-.364-.216-3.784.992.993-3.69-.237-.377a9.973 9.973 0 01-1.528-5.328c0-5.512 4.488-10 10.005-10 5.517 0 10 4.488 10 10 0 5.511-4.483 10-10 10zm5.492-7.513c-.301-.151-1.782-.879-2.059-.979-.276-.1-.477-.151-.678.151-.201.302-.779.979-.955 1.18-.176.201-.352.226-.653.075-2.225-1.117-3.6-2.584-4.577-4.275-.176-.302.176-.276.477-.879.1-.2.05-.377-.025-.528-.075-.151-.678-1.632-.93-2.235-.246-.59-.497-.502-.678-.502-.176 0-.377 0-.578 0-.201 0-.528.075-.804.377-.276.302-1.055 1.03-1.055 2.512s1.08 2.914 1.231 3.115c.151.201 2.135 3.265 5.174 4.57 2.378 1.021 3.254.912 3.864.753.844-.22 1.782-.728 2.034-1.432.251-.703.251-1.306.176-1.432-.075-.126-.276-.201-.578-.352z"/></svg>'))
                    ->color('success')
                    ->hiddenLabel()
                    ->tooltip('Send via WhatsApp')
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
                        
                        $statusEmoji = '';
                        if ($record->status === 'Resolved') $statusEmoji = "\u{2705}";
                        elseif ($record->status === 'Pending') $statusEmoji = "\u{23F3}";
                        elseif ($record->status === 'Complaint') $statusEmoji = "\u{26A0}"; // Removed \u{FE0F}
                        elseif ($record->status === 'Assigned') $statusEmoji = "\u{1F7E1}";
                        elseif ($record->status === 'Open') $statusEmoji = "\u{1F534}";
                        
                        $displayStatus .= ' ' . $statusEmoji;
                        $message .= "\n\n*Status:* " . trim($displayStatus);
                        
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
