<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroomingDetailResource\Pages;
use App\Filament\Resources\GroomingDetailResource\RelationManagers;
use App\Models\GroomingDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroomingDetailResource extends Resource
{
    protected static ?string $model = GroomingDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grooming_type')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('animal_age_type')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Textarea::make('descrpition')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grooming_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('animal_age_type')
                    ->searchable(),
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
                Tables\Actions\EditAction::make()
                ->label(''),
                Tables\Actions\DeleteAction::make()
                ->label('')
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
            'index' => Pages\ListGroomingDetails::route('/'),
            'create' => Pages\CreateGroomingDetail::route('/create'),
            'edit' => Pages\EditGroomingDetail::route('/{record}/edit'),
        ];
    }
}
