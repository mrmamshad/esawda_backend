<?php

namespace App\Filament\Resources;

use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Filament\Resources\TransactionResource\Pages\CreateTransaction;
use App\Filament\Resources\TransactionResource\Pages\EditTransaction;

/** Legacy admin: `admin/transactions.php`. */
class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Billing';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('product_name'),
            Forms\Components\TextInput::make('amount')->numeric(),
            Forms\Components\Select::make('status')->options([
                'pending' => 'Pending', 'success' => 'Success', 'failed' => 'Failed', 'cancel' => 'Cancelled',
            ]),
            Forms\Components\TextInput::make('transaction_gatway')->label('Gateway'),
            Forms\Components\TextInput::make('payment_id'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('product_name')->searchable(),
            Tables\Columns\TextColumn::make('amount')->money(),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'warning' => 'pending', 'success' => 'success', 'danger' => 'failed', 'secondary' => 'cancel',
            ]),
            Tables\Columns\TextColumn::make('transaction_gatway')->label('Gateway'),
        ])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit'   => EditTransaction::route('/{record}/edit'),
        ];
    }
}
