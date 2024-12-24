<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use App\Models\EmployeeView;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationGroup = 'Employees';
    // public static ?bool $canCreate = false; // Menonaktifkan tombol Create
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Section: Basic Information
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('phone')
                            ->label('Phone Number'),
                        TextEntry::make('status')
                            ->label('Employees Status'),
                    ])
                    ->description('Informasi tentang Employee(Pegawai)')
                        ->collapsed(false),

                // Section: Timestamps
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('hire_date')
                            ->label('Created At')
                            ->dateTime(),
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
        ->query(EmployeeView::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),
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
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->action(function ($record) {
                        // Melakukan delete pada tabel asli (bukan view)
                        $employee = Employee::find($record->id); // Temukan employee berdasarkan ID
                        if ($employee) {
                            $employee->delete(); // Menghapus data dari tabel employees
                        }
                    }),
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
            'index' => Pages\ListEmployees::route('/'),
            // 'create' => Pages\CreateEmployee::route('/create'),
            // 'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
