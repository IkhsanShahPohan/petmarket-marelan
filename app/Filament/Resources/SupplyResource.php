<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyResource\Pages;
use App\Filament\Resources\SupplyResource\RelationManagers;
use App\Models\BuyingInvoice;
use App\Models\BuyingInvoiceDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplyResource extends Resource
{
    protected static ?string $model = BuyingInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Product';

    protected static ?string $label = 'Supply';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\TextInput::make('invoice_code')
                        ->label('Invoice Code')
                        ->required(),
                       // ->default('BINV' . now()->format('YmdHis') . rand(100, 999)) // Kombinasi timestamp + angka acak
                        //->disabled(), // Tidak bisa diedit manual oleh user
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
                    ->createItemButtonLabel('Add Item')
                    ->required(),
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
                Tables\Actions\DeleteAction::make()
                ->label(''),
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
            // 'create' => Pages\CreateSupply::route('/create'),
            // 'edit' => Pages\EditSupply::route('/{record}/edit'),
        ];
    }
}
