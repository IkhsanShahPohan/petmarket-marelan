<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\UserView;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $label = 'Data-User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required(),
                Forms\Components\TextInput::make('email')
                ->required(),
                Forms\Components\TextInput::make('password')
                ->required(fn (string $context): bool => $context === 'create')
                ->dehydrated(fn ($state) => filled($state))
                ->password()
                ->revealable(),
                Forms\Components\Select::make('role')
                ->options([
                    'admin' => 'Admin',
                    'kasir' => 'Kasir',
                    'pegawai' => 'Pegawai',
                ])
                ->default('pegawai')
                ->reactive()
                ->required(),
                Repeater::make('items') // 'items' adalah input array sementara
                    ->label('Pegawai')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                        ->label('Phone')
                        ->numeric()
                        ->required(),

                        Forms\Components\DatePicker::make('hire_date')
                            ->label('Hire Date')
                            ->native(false)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Employees Status')
                            ->options([
                                'permanent' => 'Permanent',
                                'training' => 'Training',
                            ])
                            ->required(),

                        Forms\Components\Select::make('shift')
                            ->label('Employees Shift')
                            ->options([
                                'pagi' => 'Pagi',
                                'malam' => 'Malam',
                            ])
                            ->required(),

                    ])
                    ->visible(fn (callable $get) => $get('role') === 'pegawai')
                    ->required(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Section: Basic Information
                Section::make('User Info')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('role')
                            ->label('Role'),
                    ])
                ->description('Informasi tentang user.')
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
        ->query(UserView::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->searchable(),
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
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
