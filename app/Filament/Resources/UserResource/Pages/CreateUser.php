<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

// class CreateUser extends CreateRecord
// {
//     protected static string $resource = UserResource::class;
// }
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // Hapus 'items' dari form sebelum menyimpan ke database utama
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->items = $data['items'] ?? []; // Simpan sementara data repeater
        unset($data['items']); // Hapus 'items' agar tidak disimpan ke tabel utama
        return $data;

    }

    // Simpan data repeater ke tabel buying_invoice_details
    protected function afterCreate(): void
    {
        // Simpan detail dari repeater ke tabel buying_invoice_details
        foreach ($this->form->getState()['items'] as $item) {
            Employee::create([
                'user_id' => $this->record->id, // ID invoice utama
                'phone' => $item['phone'],
                'hire_date' => $item['hire_date'],
            ]);
        }

    }
    protected function redirectAfterCreate(): string
    {
        return route('filament.resources.users.index'); // Mengarah ke halaman index setelah create
    }


}
