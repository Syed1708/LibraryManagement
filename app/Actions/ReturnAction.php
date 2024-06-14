<?php
namespace App\Actions;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\Borrow;
use Illuminate\Support\Facades\Notification;

class ReturnAction extends Action
{
    public static $name = 'return';
    public static $label = 'Return';

    public function handle($record, Form $form)
    {
        return $form
            ->schema([
                DateTimePicker::make('return_date')
                    ->label('Return Date')
                    ->required(),
            ])
            ->contextual(false)
            ->save(function ($data) use ($record) {
                $borrow = Borrow::find($record->id);

                if ($borrow) {
                    $returnDate = $data['return_date'] ?? Carbon::now();
                    $borrow->returnBooks($returnDate);

                    Notification::make()
                        ->title('Books Returned')
                        ->success()
                        ->send();
                }
            });
    }
}
