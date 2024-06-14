<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\BorrowResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBorrow extends CreateRecord
{
    protected static string $resource = BorrowResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id(); // Automatically set the user_id to the authenticated user
        return $data;
    }
}
