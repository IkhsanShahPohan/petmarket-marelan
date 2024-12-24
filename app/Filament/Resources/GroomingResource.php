<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroomingResource\Pages;
use App\Filament\Resources\GroomingResource\RelationManagers;
use App\Models\Grooming;
use App\Models\GroomingView;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Tabs;
use Illuminate\Database\Eloquent\SoftDeletingScope;
    // use Filament\Tables\Components\Tabs;
    // use Filament\Tables\Actions\Action;
    // use Filament\Forms\Components\Placeholder;

    class GroomingResource extends Resource
    {
        protected static ?string $model = Grooming::class;

        protected static ?string $navigationIcon = 'heroicon-o-bars-4';
        protected static ?string $navigationGroup = 'Grooming';

        protected static ?string $label = 'Booking';

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                        ->required()
                        ->label('Customer Name'),

                        Forms\Components\TextInput::make('phone')
                        ->required()
                        ->unique(ignoreRecord: true) // Tambahkan validasi unique
                        ->helperText('Nama produk harus unik.')
                        ->label('Customer Phone'),
                        ])
                        ->createOptionUsing(function ($data) {
                            $customer = \App\Models\Customer::create([
                                'name' => $data['name'],
                                'phone' => $data['phone'],
                            ]);
                            return $customer->id; // Kembalikan ID untuk set ke product
                        })
                    ->required(),
                    Forms\Components\Select::make('grooming_detail_id')
                    ->relationship('grooming_detail','grooming_type')
                    ->options(\App\Models\GroomingDetail::pluck('grooming_type', 'id')) // Ambil opsi dari tabel
                    ->default(function () {
                    // Ambil ID kategori default dari tabel
                        return \App\Models\GroomingDetail::find(1)?->id;
                    })
                    ->label('Type')
                    ->searchable()
                    ->required(),
                    Forms\Components\TextInput::make('pet_name')
                    ->required(),
                    Forms\Components\FileUpload::make('image')
                    ->image(),
                    // Forms\Components\Select::make('category_id')
                    //     ->label('Category')
                    //     ->relationship('category', 'name') // Relasi ke nama kategori
                    //     ->options(\App\Models\Category::pluck('name', 'id')) // Ambil opsi dari tabel
                    //     ->default(function () {
                    //     // Ambil ID kategori default dari tabel
                    //         return \App\Models\GroomingDetail::find(1)?->id;
                    //     })
                    //     ->required(),
                    Forms\Components\TextInput::make('service_price')
                    ->required(),
                    Forms\Components\Select::make('status')
                    ->options([
                        'Scheduled' => 'Scheduled',
                        'In Progress' => 'In Progress',
                        'Competed' => 'Completed',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->required(),
                    // Forms\Components\DateTimePicker::make('grooming_date')
                    Forms\Components\TextInput::make('notes')
                    ->required(),
                ]);
        }

        public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Section: Basic Information3
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('customer.name')
                            ->label('Customer Name'),
                        TextEntry::make('pet_name')
                            ->label('Pet Name'),
                        TextEntry::make('grooming_detail.grooming_type')
                            ->label('Grooming Type'),
                        TextEntry::make('grooming_detail.animal_age_type')
                            ->label('Animal Age Type'),
                        ImageEntry::make('image')
                            ->label('Image'),
                    ])
                    ->description('Informasi tentang Grooming')
                        ->collapsed(false),
                        Section::make('Pricing')
                        ->schema([
                            TextEntry::make('service_price')
                            ->label('Buy Price')
                            ])
                            ->description('Informasi tentang Harga Product')
                            ->collapsed(false),

                        Section::make('Description')
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status'),
                                TextEntry::make('notes')
                                    ->label('Notes'),
                            ])
                            ->description('Informasi tentang Description')
                            ->collapsed(false),

                // Section: Timestamps
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->description('Informasi tentang waktu terbuat dan terupdate.')
                        ->collapsed(false),
            ]);
    }

        public static function table(Table $table): Table
        {
            return $table
        ->query(GroomingView::query())
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                ->searchable(),
                Tables\Columns\TextColumn::make('pet_name')->searchable(),
                // Tables\Columns\TextColumn::make('category.name')
                // ->label('Category')
                // ->searchable(),
                Tables\Columns\TextColumn::make('service_price')
                    ->label('Service Price')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        // Mengambil nilai service_price
                        $servicePrice = $record->service_price;

                        // Format menjadi format rupiah
                        return 'Rp ' . number_format($servicePrice, 0, ',', '.');
                }),
                Tables\Columns\TextColumn::make('grooming_type'),
                Tables\Columns\TextColumn::make('animal_age_type'),
                Tables\Columns\SelectColumn::make('status')
                // ->options(function () {
                //     // Ambil data status langsung dari database
                //     return \App\Models\Grooming::query()
                //         ->distinct()
                //         ->pluck('status', 'status') // Format: [key => label]
                //         ->toArray();
                // })
                ->options(\App\Models\Grooming::getStatusOptions()) // Memuat enum dari model
                ->inline()
                ->searchable(),
                Tables\Columns\TextColumn::make('notes')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
                ->filters([
                    //
                ])
                ->actions([
                    // Action::make('viewCustomer')
                    //         ->label('View Customer')
                    //         ->icon('heroicon-o-eye')
                    //         ->modalHeading('Customer Information')
                    //         ->form([
                    //             Placeholder::make('customer_id')
                    //                 ->label('Customer ID')
                    //                 ->content(fn ($record) => $record->customer->id),

                    //             Placeholder::make('customer_name')
                    //                 ->label('Customer Name')
                    //                 ->content(fn ($record) => $record->customer->name),
                    //         ])
                    //         ->action(fn (Customer $customer) => $customer->customers),
                    Tables\Actions\EditAction::make()
                    ->label(''),
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
                'index' => Pages\ListGroomings::route('/'),
                // 'create' => Pages\CreateGrooming::route('/create'),
                'edit' => Pages\EditGrooming::route('/{record}/edit'),
                'view' => Pages\ViewGrooming::route('/{record}'),
            ];
        }
    }
