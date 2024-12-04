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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Product';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_name')
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
                Forms\Components\TextInput::make('product_side_effect')
                ->required(),
                Forms\Components\TextInput::make('product_description')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')->searchable(),
                Tables\Columns\TextColumn::make('category.name') // Relasi ke nama kategori
                ->label('Category')
                ->sortable(), // Opsional: memungkinkan pengurutan
                Tables\Columns\TextColumn::make('sell_price')->searchable(),
                Tables\Columns\TextColumn::make('stock')->searchable(),
                Tables\Columns\TextColumn::make('product_side_effect')->searchable(),
                Tables\Columns\TextColumn::make('product_description')->searchable(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
