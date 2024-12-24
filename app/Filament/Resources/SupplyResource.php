<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyResource\Pages;
use App\Filament\Resources\SupplyResource\RelationManagers;
use App\Models\BuyingInvoice;
use App\Models\BuyingInvoiceDetail;
use Filament\Forms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class SupplyResource extends Resource
{
    protected static ?string $model = BuyingInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Product';

    protected static ?string $label = 'Supply';
    public static function shouldRegisterNavigation(): bool
    {
        // Only show the page for users with the 'kasir' role
        return auth()->user()->role === 'admin';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\TextInput::make('invoice_code')
                        ->label('Invoice Code')
                        ->required()
                       ->default('BINV' . now()->format('YmdHis')),// Kombinasi timestamp + angka acak
                        // ->disabled(), // Tidak bisa diedit manual oleh user
                    Forms\Components\Select::make('supplier_id')
                        ->label('Supplier')
                        ->relationship('supplier', 'name')
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Supplier Name'),
                            Forms\Components\TextInput::make('contact_name')
                            ->required()
                            ->label('Contact Name'),
                            Forms\Components\TextInput::make('phone')
                            ->required()
                            //->unique(ignoreRecord: true) // Tambahkan validasi unique
                            ->helperText('Nomor telepon harus unik.')
                            ->label('Supplier Phone'),
                            Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->label('Supplier email'),
                            Forms\Components\TextInput::make('address')
                            ->required()
                            ->label('Supplier address'),
                        ])
                        ->createOptionUsing(function ($data) {
                            $supplier = \App\Models\Supplier::create([
                                'name' => $data['name'],
                                'contact_name' => $data['contact_name'],
                                'phone' => $data['phone'],
                                'email' => $data['email'],
                                'address' => $data['address'],
                            ]);
                            return $supplier->id;
                        }),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'Paid' => 'Paid',
                            'Cancelled' => 'Cancelled',
                        ])
                        ->default('Paid')
                        ->required(),
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->label('Image'),

                // Schema untuk tabel kedua (detail item invoice)
                Repeater::make('items') // 'items' adalah input array sementara
                    ->label('Invoice Details')
                    ->schema([
                        // Forms\Components\Select::make('invoice_id')
                        //     ->relationship('invoice', 'id')
                        //     ->required(),
                        Forms\Components\TextInput::make('name_product')
                        ->label('Product Name')
                        ->required(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->required(),

                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->required(),
                    ])
                    // ->hidden(fn () => !auth()->user()->can('manageDetails', BuyingInvoice::class))
                    ->createItemButtonLabel('Add Item')
                    ->required(fn ($context) => $context === 'create')
                    ->disabled(fn ($context) => $context === 'edit'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Invoice Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Invoice ID'),
                        TextEntry::make('supplier.name')
                            ->label('Supplier_id'),
                        TextEntry::make('invoice_code')
                            ->label('invoice_code'),
                        TextEntry::make('status')
                            ->label('Status'),
                        ImageEntry::make('image')
                            ->label('Invoice Image')
                            ->size(150) // Ukuran gambar
                            ->circular(), // Bentuk lingkaran
                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d M Y, H:i')),
                    ])
                        ->description('Informasi utama tentang invoice.')
                        ->collapsed(false),
                Section::make('Invoice Details')
                    ->schema([
                        TextEntry::make('id')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                // Panggil stored procedure untuk mendapatkan detail invoice
                                $invoiceId = $record->id;
                                $invoiceDetails = DB::select('CALL GetBuyingInvoiceDetails(?)', [$invoiceId]);

                                // Variabel untuk menyusun hasil
                                $details = '';

                                // Iterasi melalui hasil prosedur
                                foreach ($invoiceDetails as $detail) {
                                    $formattedPrice = number_format($detail->product_price, 0, ',', '.');
                                    $details .= "Invoice Code: {$detail->invoice_code}\n";
                                    $details .= "Supplier: {$detail->supplier_name} ({$detail->supplier_email})\n";
                                    $details .= "Product: {$detail->product_name} (Price: Rp {$formattedPrice}, Quantity: {$detail->product_quantity})\n";
                                    $details .= "Status: {$detail->status}\n";
                                    $details .= "-----------------------------\n";
                                }

                                // Panggil fungsi untuk menghitung total belanjaan
                                $totalBelanjaan = DB::selectOne('SELECT calculate_buying_invoice_total(?) AS total', [$invoiceId]);
                                $formattedTotal = number_format($totalBelanjaan->total, 0, ',', '.');

                                // Tambahkan total belanjaan ke dalam tampilan
                                $details .= "Total Belanjaan: Rp {$formattedTotal}\n";

                                return $details; // Mengganti newline agar tampil di HTML
                            }),
                            ])
                            ->description('Detail produk pada invoice.')
                            ->collapsed(false)
                            ->extraAttributes([
                                'style' => 'white-space: pre-line; line-height: 0;', // Atur jarak antar baris
                            ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_code')->label('Invoice Code'),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier Name'),
                Tables\Columns\TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\DeleteAction::make()
                // ->label(''),
                Tables\Actions\ViewAction::make()
                ->label(''),
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
            'index' => Pages\ListSupplies::route('/'),
            'create' => Pages\CreateSupply::route('/create'),
            'edit' => Pages\EditSupply::route('/{record}/edit'),
        ];
    }
}
