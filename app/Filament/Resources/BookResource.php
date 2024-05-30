<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Book;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BookResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookResource\RelationManagers;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Section 1')
                    ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        Forms\Components\TextInput::make('isbn')
                            ->readOnly(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->fileAttachmentsDirectory('books/images')
                            ->columnSpanFull(),

                        Select::make('authors')
                            ->label('Co-authors')
                            ->relationship('authors', 'name')
                            ->multiple()
                            ->required()
                            ->preload()
                            ->searchable(),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'title')
                            ->required()
                            ->preload()
                            ->searchable(),
                        Select::make('genre_id')
                            ->label('Genre')
                            ->relationship('genre', 'title')
                            ->required()
                            ->preload()
                            ->searchable(),

                    ])->columnSpan(2)->columns(2),

                Group::make()->schema([
                    Section::make('Book Image')
                        ->description('The items you have selected for purchase')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->image()
                                ->disk('public')
                                ->directory('books/thumb')
                                ->required(),

                        ])->collapsible()->columnSpan(1),
                    Section::make('Meta')->schema([
                        Forms\Components\DatePicker::make('published_year')
                            ->required(),
                        Forms\Components\TextInput::make('copices')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('avilable_copices')
                            ->required()
                            ->maxLength(255),
                    ]),


                ]),




            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('isbn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('authors.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('genre.title')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('published_year')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('copices')
                    ->searchable(),
                Tables\Columns\TextColumn::make('avilable_copices')
                    ->searchable(),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
