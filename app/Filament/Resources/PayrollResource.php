<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Payroll;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Employees';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Employee')
                    ->options(function () {
                        // Mengambil employee_id dan nama dari tabel users melalui relasi
                        return Employee::all()->pluck('user.name', 'id');
                    })
                    ->required(),
                Forms\Components\DatePicker::make('month')
                    ->label('Month')
                    ->format('Y-m')
                    ->required(),
                Forms\Components\Hidden::make('base_salary'),
                    // ->required(),
                Forms\Components\TextInput::make('food_salary')
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('transport_salary')
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('bonus')
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('deductions')
                    ->numeric()
                    ->default(0.00),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label('Employee Name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                ->label('Month'),
                Tables\Columns\TextColumn::make('total_salary')
                ->label('Total Salary')
                ->getStateUsing(function (Payroll $record) {
                    // Memanggil prosedur dan mendapatkan total_salary
                    $result = DB::selectOne('CALL GetPayrollSummary(?, ?)', [
                        $record->employee_id,    // ID Karyawan
                        $record->month,          // Bulan payroll
                    ]);

                    // Mengembalikan format rupiah jika ada hasil, jika tidak return 0
                    $totalSalary = $result ? $result->total_salary : 0;

                    // Format menjadi format rupiah
                    return 'Rp ' . number_format($totalSalary, 0, ',', '.');
                })

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
                // Tables\Actions\EditAction::make()
                // ->label(''),
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
            'index' => Pages\ListPayrolls::route('/'),
            // 'create' => Pages\CreatePayroll::route('/create'),
            // 'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
