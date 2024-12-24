<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductView;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ImageEntry;
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

public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Section: Basic Information
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('product_name')
                            ->label('Product Name'),
                        TextEntry::make('category_name')
                            ->label('Category Name'),
                        TextEntry::make('stock')
                            ->label('Stock'),
                        ImageEntry::make('image')
                            ->label('Image'),
                    ])
                    ->description('Informasi tentang Product')
                        ->collapsed(false),

                        Section::make('Pricing')
                        ->schema([
                            TextEntry::make('buy_price')
                            ->label('Buy Price'),
                            TextEntry::make('sell_price')
                            ->label('Sell Price'),
                            ])
                            ->description('Informasi tentang Harga Product')
                            ->collapsed(false),

                        Section::make('Description')
                            ->schema([
                                TextEntry::make('side_effect')
                                    ->label('Side Effect'),
                                TextEntry::make('description')
                                    ->label('Description'),
                            ])
                            ->description('Informasi tentang Product')
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
        ->query(ProductView::query())
        ->columns([
            Tables\Columns\TextColumn::make('product_name')->searchable(),
            Tables\Columns\TextColumn::make('category_name'), // Relasi ke nama kategori
            Tables\Columns\ImageColumn::make('image')
                ->label('Category')
                ->sortable(), // Opsional: memungkinkan pengurutan
            Tables\Columns\TextColumn::make('buy_price')
                ->searchable()
                ->getStateUsing(function ($record) {
                    $buy_price = $record->buy_price;
                    return $buy_price ? 'Rp ' . number_format($buy_price, 0, ',', '.') : 'Rp 0';
                }),
            Tables\Columns\TextColumn::make('sell_price')
                ->searchable()
                ->getStateUsing(function ($record) {
                    $sellPrice = $record->sell_price;
                    return $sellPrice ? 'Rp ' . number_format($sellPrice, 0, ',', '.') : 'Rp 0';
                }),
            Tables\Columns\TextColumn::make('stock')->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                    // ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // Define your filters here
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
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }
}
