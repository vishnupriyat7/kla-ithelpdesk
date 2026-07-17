<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorComplaintResource\Pages;
use App\Filament\Resources\VendorComplaintResource\RelationManagers;
use App\Models\VendorComplaint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorComplaintResource extends Resource
{
    protected static ?string $model = VendorComplaint::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Complaint Register';
    protected static ?string $modelLabel = 'Vendor Complaint Register';
    protected static ?string $pluralModelLabel = 'Vendor Complaint Register';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->getRoleName() !== 'cowd';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Complaint Details')
                    ->schema([
                        Forms\Components\Select::make('vendor')
                            ->label('Vendor')
                            ->options([
                                'IHRD' => 'IHRD',
                                'Lipi' => 'Lipi',
                                'Aser' => 'Aser',
                            ])
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('vendor_complaint_no')
                            ->label('Vendor Complaint No')
                            ->options(
                                fn() => \App\Models\ComplaintRegister::whereNotNull('vendor_complaint_id')
                                    ->where('vendor_complaint_id', '!=', '')
                                    ->distinct()
                                    ->pluck('vendor_complaint_id', 'vendor_complaint_id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Unattended' => 'Unattended',
                                'Pending Spare' => 'Pending Spare',
                                'Resolved' => 'Resolved',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('complaint_description')
                            ->label('Complaint Description')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('chm_remark')
                            ->label('CHM Remark')
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('service_reports')
                            ->label('Service Reports')
                            ->schema([
                                Forms\Components\TextInput::make('component')
                                    ->label('Component')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('report_no')
                                    ->label('Report No')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('date')
                                    ->label('Date'),
                                Forms\Components\TextInput::make('staff')
                                    ->label('Service Staff')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Service Report Description')
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Service Report Image')
                                    ->image()
                                    ->openable()
                                    ->downloadable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->disabled(fn() => auth()->user()->getRoleName() === 'chm')
                            ->defaultItems(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('Sl.no')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Registered By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('complaintTicket.ticket_no')
                    ->label('Internal Ticket No')
                    ->badge()
                    ->url(fn($record) => $record->complaint_ticket_id ? \App\Filament\Resources\ComplaintRegisterResource::getUrl('view', ['record' => $record->complaint_ticket_id]) : null)
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor_complaint_no')
                    ->label('Vendor Complaint No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Un attended' => 'danger',
                        'Pending Spare' => 'warning',
                        'Resolved' => 'success',
                        default => 'secondary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('complaint_description')
                    ->label('Vendor Complaint Description')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('chm_remark')
                    ->label('Remark')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-M-Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-M-Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Un attended' => 'Un attended',
                        'Pending Spare' => 'Pending Spare',
                        'Resolved' => 'Resolved',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorComplaints::route('/'),
            'create' => Pages\CreateVendorComplaint::route('/create'),
            'view' => Pages\ViewVendorComplaint::route('/{record}'),
            'edit' => Pages\EditVendorComplaint::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
