<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages\CreateTestimonial;
use App\Filament\Resources\TestimonialResource\Pages\EditTestimonial;
use App\Filament\Resources\TestimonialResource\Pages\ListTestimonials;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Homepage "What our community says" cards + seller-profile testimonials strip. */
class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Testimonials';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Author name')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('designation')
                ->label('Designation (e.g. Online Seller)')
                ->maxLength(100),
            Forms\Components\Textarea::make('content')
                ->label('Comment')
                ->required()
                ->rows(5)
                ->columnSpanFull(),
            Forms\Components\FileUpload::make('image')
                ->label('Author photo')
                ->disk('public')
                ->directory('testimonials')
                ->image()
                ->imagePreviewHeight('160')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(4096),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image_url')->label('Photo')->circular(),
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('designation')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('content')->label('Comment')->limit(80)->wrap(),
        ])->defaultSort('id', 'desc')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
