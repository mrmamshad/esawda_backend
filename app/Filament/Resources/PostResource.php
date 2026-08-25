<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Legacy admin: `admin/posts.php`. */
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ads';

    protected static ?string $label = 'Ad';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('product_name')->required()->maxLength(150),
            Forms\Components\Textarea::make('description')->rows(6)->columnSpanFull(),
            Forms\Components\Select::make('status')->required()->options([
                'pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected', 'expire' => 'Expired',
            ]),
            Forms\Components\TextInput::make('price')->numeric(),
            Forms\Components\Select::make('featured')->options(['0' => 'No', '1' => 'Yes']),
            Forms\Components\Select::make('urgent')->options(['0' => 'No', '1' => 'Yes']),
            Forms\Components\Select::make('highlight')->options(['0' => 'No', '1' => 'Yes']),
            Forms\Components\TextInput::make('city'),
            Forms\Components\TextInput::make('country'),
            Forms\Components\TextInput::make('phone'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('product_name')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('price')->sortable()->money(),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'warning' => 'pending', 'success' => 'active', 'danger' => 'rejected', 'secondary' => 'expire',
                ]),
                Tables\Columns\IconColumn::make('featured')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected', 'expire' => 'Expired',
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
