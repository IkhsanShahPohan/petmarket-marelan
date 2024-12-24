<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\SupplyResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\BuyingInvoiceDetail;
use Illuminate\Support\Facades\DB;

class CreateSupply extends CreateRecord
{
    protected static string $resource = SupplyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    // Hapus 'items' dari form sebelum menyimpan ke database utama
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->items = $data['items'] ?? []; // Simpan sementara data repeater
        unset($data['items']); // Hapus 'items' agar tidak disimpan ke tabel utama
        return $data;
        $data['invoice_code'] = 'BINV' . now()->format('YmdHis') . rand(100, 999);
        return $data;
    }

    // Simpan data repeater ke tabel buying_invoice_details
    protected function afterCreate(): void
    {
    // Simpan detail dari repeater ke tabel buying_invoice_details
    foreach ($this->form->getState()['items'] as $item) {
        BuyingInvoiceDetail::create([
            'invoice_id' => $this->record->id, // ID invoice utama
            'name_product' => $item['name_product'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
        ]);
    }

    // Hitung total_price dari stored function berdasarkan invoice_id yang benar
    // $totalPrice = DB::selectOne('SELECT calculate_total_price_buying(?) AS total', [$this->record->id])->total;

    // // Update total_price di record utama
    // $this->record->update([
    //     'total_price' => $totalPrice,
    // ]);
    }
}
