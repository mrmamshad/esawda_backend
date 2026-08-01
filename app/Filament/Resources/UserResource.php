<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;

/**
 * Legacy admin: `admin/users.php`. Manage registered users.
 * Uses the same `ad_user` table via the Eloquent User model.
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('username')->required()->maxLength(40),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\TextInput::make('name')->maxLength(225),
            Forms\Components\Select::make('user_type')->options(['user' => 'User', 'seller' => 'Seller']),
            Forms\Components\Select::make('status')->options(['0' => 'Pending', '1' => 'Active', '2' => 'Banned']),
            Forms\Components\TextInput::make('group_id')->default('free'),
            Forms\Components\TextInput::make('phone'),
            Forms\Components\TextInput::make('country'),
            Forms\Components\TextInput::make('city'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('username')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'warning' => '0', 'success' => '1', 'danger' => '2',
                ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['0' => 'Pending', '1' => 'Active', '2' => 'Banned']),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}
