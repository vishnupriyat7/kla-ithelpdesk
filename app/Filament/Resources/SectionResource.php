<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\Pages;
use App\Models\Section;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions;
use Illuminate\Support\Facades\Auth;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function canViewAny(): bool
    {
        return auth()->check() && !in_array(auth()->user()->getRoleName(), ['chm', 'hardwareadmin']);
    }
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Sections';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Select::make('status')
                    ->options([
                        '1' => '1',
                        '0' => '0'
                    ])
                    ->required()
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make()->label(''),
                Actions\DeleteAction::make()->label('')
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }

}
