<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $recordTitleAttribute = 'name';

    // Hide User Management for non-superadmin users
    protected static bool $shouldRegisterNavigation = false;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignorable: fn($record) => $record)
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->label('PEN / Attendance ID')
                    ->unique(ignorable: fn($record) => $record)
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->visible(fn(string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\Select::make('role_id')
                    ->relationship('role', 'name', fn (Builder $query) => $query->orderBy('id'))
                    ->preload()
                    ->searchable()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('PEN / Attendance ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role.name')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
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
                Tables\Filters\SelectFilter::make('role_id')
                    ->relationship('role', 'name')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-m-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset User Password')
                    ->modalDescription('Are you sure you want to reset this user\'s password? It will be set to today\'s date (ddmmyy).')
                    ->action(function (User $record) {
                        $newPassword = now()->format('dmy');
                        $record->update([
                            'password' => Hash::make($newPassword),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Password Reset Successfully')
                            ->body("New password for {$record->name} is: {$newPassword}")
                            ->success()
                            ->send();
                    })
                    ->visible(fn() => Auth::user()->isSuperAdmin()),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('role', function (Builder $query) {
            $query->where('name', '!=', 'superadmin');
        });
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }
}