<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Book;
use App\Models\User;
use Filament\Tables;
use App\Models\Borrow;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Actions\ReturnAction;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BorrowResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BorrowResource\RelationManagers;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Student Details')
                        ->schema([
                            Forms\Components\TextInput::make('borrow_no')
                                ->default('BR-' . random_int(100000, 9999999))
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            Forms\Components\Select::make('student_id')
                                ->label('Student')
                                ->options(User::where('role', 'STUDENT')->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                        ]),

                    Forms\Components\Wizard\Step::make('Borrows Details')
                        ->schema([
                            Section::make("Basic Info")->schema([
                                DatePicker::make('borrow_date')
                                    ->default(now())
                                    // ->disabled()
                                    ->required(),
                                DatePicker::make('due_date')
                                    ->default(Carbon::now()->addDays(14))
                                    // ->disabled()
                                    ->required(),
                                Forms\Components\TextInput::make('fees')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('late_fee')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('payable')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),

                            ])->columns(5),

                            Repeater::make('items')
                                ->label('Books')
                                ->relationship()
                                ->collapsible()
                                ->schema([
                                    Forms\Components\Select::make('book_id')
                                        ->label('Book')
                                        ->options(Book::query()->pluck('title', 'id'))
                                        ->preload()
                                        ->searchable()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set, $get) {
                                            $book = Book::find($state);
                                            $set('book_title', $book->title ?? null);
                                            $set('fees', $book->fees ?? 0);
                                            $set('available_copies', $book->available_copies ?? 0);
                                            $set('qty', 0);
                                        }),
                                    Forms\Components\Hidden::make('title'),

                                    Forms\Components\TextInput::make('fees')
                                        ->required()
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(),
                                    Forms\Components\TextInput::make('late_fee')
                                        ->required()
                                        ->numeric()
                                        ->default(0)
                                        ->minLength(1),

                                    TextInput::make('qty')
                                        ->required()
                                        ->numeric()
                                        ->live()
                                        ->dehydrated()
                                        ->minValue(1)
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            $availableCopies = $get('available_copies');
                                            if ($state > $availableCopies) {
                                                $set('qty', $availableCopies); // Set qty to the max available copies
                                            }
                                        }),
                                    Forms\Components\Placeholder::make('available_copies')
                                        ->label('Available Copies')
                                        ->content(fn ($get) => $get('available_copies')),
                                    Forms\Components\Placeholder::make('total_price')
                                        ->label('Total Price')
                                        ->content(function ($get) {
                                            return $get('qty') * $get('fees');
                                        }),

                                ])
                                ->columns(6)
                                ->itemLabel(fn (array $state): ?string => $state['book_title'] ?? null)
                                ->reorderableWithDragAndDrop(false)
                                ->addActionLabel('Add More')
                                ->afterStateUpdated(function ($state, $set) {
                                    $totalFees = 0;
                                    $totalLateFees = 0;

                                    foreach ($state as $item) {
                                        $totalFees += $item['fees'] * $item['qty'];
                                        $totalLateFees += $item['late_fee'];
                                    }

                                    $set('fees', $totalFees);
                                    $set('late_fee', $totalLateFees);
                                    $set('payable', $totalFees + $totalLateFees);
                                }),
                        ]),

                ])->columnSpanFull(),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('librarian.name')
                    ->sortable(),
                TextColumn::make('student.name')->label('Student Name'),
                TextColumn::make('borrow_no')->label('Borrow No'),
                TextColumn::make('borrow_date')->label('Borrow Date'),
                TextColumn::make('due_date')->label('Deadline'),
                TextColumn::make('return_date')->label('Return Date'),
                TextColumn::make('fees')->label('Fees'),
                TextColumn::make('late_fee')->label('Late Fee'),
                TextColumn::make('payable')->label('Total Payable'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('PDF')
                ->icon('heroicon-o-rectangle-stack')
                ->url(fn(Borrow $record) => route('student.pdf.download', $record))
                ->openUrlInNewTab()
                ,
                Action::make('return')
                    ->label('Return')
                    ->requiresConfirmation()
                    // ->action(ReturnAction::class)
                    // ->disabled(fn ($record) => $record->return_date !== null),
                    ->action(function ($record) {
                        $borrow = Borrow::find($record->id);
                        if ($borrow) {
                            $returnDate = Carbon::now();
                            $borrow->returnBooks($returnDate);

                            Notification::make()
                                ->title('Books Returned')
                                ->success()
                                ->send();
                        }
                    })->disabled(fn ($record) => $record->return_date !== null),

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
            'index' => Pages\ListBorrows::route('/'),
            'create' => Pages\CreateBorrow::route('/create'),
            'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }
}
