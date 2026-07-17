<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintTypeResource\Pages;
use App\Filament\Resources\ComplaintTypeResource\RelationManagers;
use App\Models\ComplaintType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ComplaintTypeResource extends Resource
{
    protected static ?string $model = ComplaintType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && in_array(auth()->user()->getRoleName(), ['admin', 'superadmin', 'hardwareadmin']);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && in_array(auth()->user()->getRoleName(), ['admin', 'superadmin', 'hardwareadmin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListComplaintTypes::route('/'),
            'create' => Pages\CreateComplaintType::route('/create'),
            'edit' => Pages\EditComplaintType::route('/{record}/edit'),
        ];
    }
}
