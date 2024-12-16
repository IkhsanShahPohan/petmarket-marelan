<?php

namespace App\Filament\Resources\InvoiceViewResource\Pages;

use App\Filament\Resources\InvoiceViewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceView extends EditRecord
{
    protected static string $resource = InvoiceViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
