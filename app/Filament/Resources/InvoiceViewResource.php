<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceViewResource\Pages;
use App\Models\InvoiceView;
use App\Models\SellingInvoiceDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan package ini terinstall
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

class InvoiceViewResource extends Resource
{
    protected static ?string $model = InvoiceView::class;
    protected static ?string $navigationGroup = 'Kasir';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status'),
                Forms\Components\Textarea::make('notes')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('total')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('invoice_date')
                    ->required(),
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
                        TextEntry::make('status')
                            ->label('Status'),
                        TextEntry::make('notes')
                            ->label('Catatan'),
                        TextEntry::make('invoice_date')
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
                                $invoiceId = $record->id;
                                $invoiceDetails = DB::select('CALL GetInvoiceDetails(?)', [$invoiceId]);

                                $details = '';
                                foreach ($invoiceDetails as $detail) {
                                    $formattedPrice = number_format($detail->product_price, 0, ',', '.');
                                    $details .= "{$detail->product_name} (Rp {$formattedPrice}, Quantity: {$detail->product_quantity})\n";
                                }

                                // Menghitung total belanjaan menggunakan fungsi yang sudah dibuat di database
                                $totalBelanjaan = DB::select('SELECT calculate_invoice_total(?) AS total', [$invoiceId]);

                                // Menambahkan total belanjaan di akhir detail produk
                                $details .= "============================= +\nTotal Belanjaan: Rp " . number_format($totalBelanjaan[0]->total, 0, ',', '.');

                                return $details ?: 'Tidak ada detail produk untuk invoice ini.';
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('total')
                ->numeric()
                ->sortable()
                ->getStateUsing(function ($record) {
                    // Mengambil nilai total
                    $total = $record->total;

                    // Format menjadi format rupiah
                    return 'Rp ' . number_format($total, 0, ',', '.');
                }),
                Tables\Columns\TextColumn::make('invoice_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('Today')
                    ->query(fn (Builder $query) => $query->whereDate('invoice_date', now()->toDateString()))
                    ->label('Hari ini'),

                Tables\Filters\Filter::make('This Month')
                    ->query(fn (Builder $query) => $query->whereMonth('invoice_date', now()->month)
                                                        ->whereYear('invoice_date', now()->year))
                    ->label('Bulan ini'),

                Tables\Filters\Filter::make('This Year')
                    ->query(fn (Builder $query) => $query->whereYear('invoice_date', now()->year))
                    ->label('Tahun ini'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('generate_invoice')
                    ->label('Generate Invoice')
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('primary')
                    ->url(fn (InvoiceView $record) => route('invoice.pdf', $record->id))
            ])
            ->defaultSort('id', 'desc');
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoiceViews::route('/'),
            // 'create' => Pages\CreateInvoiceView::route('/create'),
            // 'edit' => Pages\EditInvoiceView::route('/{record}/edit'),
        ];
    }
}
