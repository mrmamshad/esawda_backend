<?php

namespace App\Filament\Resources;

use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\BlogResource\Pages\ListBlogs;
use App\Filament\Resources\BlogResource\Pages\CreateBlog;
use App\Filament\Resources\BlogResource\Pages\EditBlog;

/** Legacy admin: `admin/blog.php`. */
class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationIcon  = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\Textarea::make('description')->rows(10)->columnSpanFull(),
            Forms\Components\TextInput::make('tags'),
            Forms\Components\Select::make('status')->options(['publish' => 'Publish', 'pending' => 'Pending'])->default('publish'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('title')->searchable()->limit(60),
            Tables\Columns\BadgeColumn::make('status')->colors(['success' => 'publish', 'warning' => 'pending']),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->defaultSort('id', 'desc')
          ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBlogs::route('/'),
            'create' => CreateBlog::route('/create'),
            'edit'   => EditBlog::route('/{record}/edit'),
        ];
    }
}
