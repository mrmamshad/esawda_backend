<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Legacy admin: `admin/category.php`. */
class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Ads';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('cat_name')->required()->maxLength(300),
            Forms\Components\TextInput::make('slug')->maxLength(150),
            Forms\Components\TextInput::make('icon')->default('fa-usd'),
            Forms\Components\TextInput::make('cat_order')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('cat_id')->sortable(),
            Tables\Columns\TextColumn::make('cat_name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug'),
            Tables\Columns\TextColumn::make('cat_order')->sortable(),
        ])
            ->defaultSort('cat_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
