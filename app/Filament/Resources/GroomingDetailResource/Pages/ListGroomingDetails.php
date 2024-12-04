<?php

namespace App\Filament\Resources\GroomingDetailResource\Pages;

use App\Filament\Resources\GroomingDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroomingDetails extends ListRecords
{
    protected static string $resource = GroomingDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
