<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Product';

    protected static ?string $label = 'Stock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required(),
                Forms\Components\Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name') // Relasi ke nama kategori
                ->required(),
                Forms\Components\TextInput::make('sell_price')
                ->numeric()
                ->required(),
                Forms\Components\TextInput::make('buy_price')
                ->numeric()
                ->required(),
                Forms\Components\TextInput::make('stock')
                ->required(),
                Forms\Components\FileUpload::make('image')
                ->image()
                ->required(),
                Forms\Components\TextInput::make('side_effect')
                ->required(),
                Forms\Components\TextInput::make('description')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('category.name') // Relasi ke nama kategori
                ->label('Category')
                ->sortable(), // Opsional: memungkinkan pengurutan
                Tables\Columns\TextColumn::make('sell_price')
    ->searchable()
    ->getStateUsing(function ($record) {
        // Mengambil nilai sell_price
        $sellPrice = $record->sell_price;

        // Format menjadi format rupiah
        return $sellPrice ? 'Rp ' . number_format($sellPrice, 0, ',', '.') : 'Rp 0';
    }),
                Tables\Columns\TextColumn::make('stock')->searchable(),
                Tables\Columns\TextColumn::make('side_effect')->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('description')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label(''),
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
            'index' => Pages\ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
