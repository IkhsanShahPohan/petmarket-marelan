<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use App\Enums\StatusEnum;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationGroup = 'Employees';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Hidden::make('employee_id')
                    ->default(fn () => Auth::user()?->employee?->id)
                    ->required(),

                    Forms\Components\Radio::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                    ])
                    ->reactive()
                    //->mapWithKeys(fn($value) => [$value => $value])) // Mengatur enum menjadi key-value
                    ->required(),

                Forms\Components\TextInput::make('notes')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\FileUpload::make('image')
                    // ->required()
                    ->image(),
                Forms\Components\Radio::make('type')
                    ->label('Waktu Kehadiran')
                    ->options([
                        'datang' => 'Datang',
                        'pulang' => 'Pulang',
                    ])
                    ->visible(fn (callable $get) => $get('status') === 'present')
                    ->required(),

                Forms\Components\Select::make('request_status')
                    ->label('Request Status')
                    ->options([
                        'menunggu' => 'Menunguu',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                    ])
                    // ->reactive()
                    ->visible(fn () => Auth::user()->isAdmin())
                    ->disabled(fn () => !Auth::user()->isAdmin())
                    //->mapWithKeys(fn($value) => [$value => $value])) // Mengatur enum menjadi key-value
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->query(
            //     fn (Builder $query) => $query->when(auth()->user()->employee, function ($query) {
            //         $query->where('employee_id', auth()->user()->employee ? auth()->user()->employee->id : null);
            //     })
            // )

            ->columns([
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label('Employee Name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                    // ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),

                // // Menampilkan status dalam bentuk teks untuk pegawai
                // Tables\Columns\TextColumn::make('request_status')
                //     ->inline()
                //     ->visible(fn () => Auth::user()->isEmployee()) // Hanya visible untuk pegawai
                //     ->searchable(),

                // Dropdown untuk Admin
                Tables\Columns\SelectColumn::make('request_status')
                    ->options(\App\Models\Attendance::getStatusOptions()) // Memuat enum dari model
                    ->inline()
                    // ->visible(fn () => Auth::user()->isAdmin()) // Hanya visible untuk admin
                    ->disabled(fn () => !Auth::user()->isAdmin()) // Menonaktifkan untuk non-admin
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('Today')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', now()->toDateString()))
                    ->label('Hari ini'),

                Tables\Filters\Filter::make('This Month')
                    ->query(fn (Builder $query) => $query->whereMonth('created_at', now()->month)
                                                        ->whereYear('created_at', now()->year))
                    ->label('Bulan ini'),

                Tables\Filters\Filter::make('This Year')
                    ->query(fn (Builder $query) => $query->whereYear('created_at', now()->year))
                    ->label('Tahun ini'),

                Tables\Filters\Filter::make('Approved')
                    ->query(fn (Builder $query) => $query->where('request_status', 'diterima'))
                    ->label('Diterima'),

                Tables\Filters\Filter::make('Pending')
                    ->query(fn (Builder $query) => $query->where('request_status', 'menunggu'))
                    ->label('Menunggu'),

                Tables\Filters\Filter::make('Rejected')
                    ->query(fn (Builder $query) => $query->where('request_status', 'ditolak'))
                    ->label('Ditolak'),
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
            'index' => Pages\ListAttendances::route('/'),
            // 'create' => Pages\CreateAttendance::route('/create'),
            // 'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
