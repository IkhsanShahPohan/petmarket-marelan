<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroomingResource\Pages;
use App\Filament\Resources\GroomingResource\RelationManagers;
use App\Models\Grooming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroomingResource extends Resource
{
    protected static ?string $model = Grooming::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Grooming';

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
                ->searchable()
                ->required(),
                Forms\Components\TextInput::make('pet_name')
                ->required(),
                Forms\Components\Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name') // Relasi ke nama kategori
                ->default('grooming')
                ->required(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                ->searchable(),
                Tables\Columns\TextColumn::make('pet_name')->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                ->label('Category')
                ->searchable(),
                Tables\Columns\TextColumn::make('service_price')->searchable(),
                Tables\Columns\TextColumn::make('grooming_date')->searchable(),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'create' => Pages\CreateGrooming::route('/create'),
            'edit' => Pages\EditGrooming::route('/{record}/edit'),
        ];
    }
}
