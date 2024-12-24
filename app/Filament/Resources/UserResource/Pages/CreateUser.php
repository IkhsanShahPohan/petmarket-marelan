<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

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
        try {
            DB::transaction(function () {
                foreach ($this->items as $item) {
                    Employee::create([
                        'user_id' => $this->record->id, // ID user utama
                        'phone' => $item['phone'],
                        'hire_date' => $item['hire_date'],
                    ]);
                }
            });
        } catch (QueryException $exception) {
            // Tangani error SQL
            session()->flash('error', 'Terjadi kesalahan pada database: ' . $exception->getMessage());
            $this->redirectToUsersPage();
        }
    }

    // Redirect ke halaman index jika terjadi error
    protected function redirectToUsersPage(): void
    {
        $this->redirect(route('filament.resources.users.index'));
        exit; // Pastikan redirect langsung berhenti di sini
    }

    // Redirect setelah berhasil create
    protected function redirectAfterCreate(): string
    {
        return route('filament.resources.users.index'); // Mengarah ke halaman index setelah create
    }
}
