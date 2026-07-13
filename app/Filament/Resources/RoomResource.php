<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Rooms';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Room Name/Number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('floor_id')
                    ->label('Floor')
                    ->relationship('floorLevel', 'name', fn (Builder $query) => $query->orderBy('sort_order'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('block')
                    ->maxLength(255),
                Forms\Components\Select::make('office_location_id')
                    ->label('Building')
                    ->relationship('location', 'location')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Toggle::make('is_office')
                    ->label('Is this an office?')
                    ->default(true),
                Forms\Components\Textarea::make('comment')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('floorLevel.name')
                    ->label('Floor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.location')
                    ->label('Building')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_office')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('block')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('office_location_id')
                    ->label('Building')
                    ->relationship('location', 'location'),
                Tables\Filters\SelectFilter::make('floor_id')
                    ->label('Floor')
                    ->relationship('floorLevel', 'name', fn (Builder $query) => $query->orderBy('sort_order')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
