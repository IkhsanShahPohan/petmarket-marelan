<?php

namespace App\Filament\Resources\GroomingDetailResource\Pages;

use App\Filament\Resources\GroomingDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroomingDetail extends EditRecord
{
    protected static string $resource = GroomingDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
