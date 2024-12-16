<?php

namespace App\Filament\Resources\InvoiceViewResource\Pages;

use App\Filament\Resources\InvoiceViewResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use App\Models\InvoiceView;
use Carbon\Carbon;

class ListInvoiceViews extends ListRecords
{
    protected static string $resource = InvoiceViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_report')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->modalWidth(MaxWidth::Medium)
                ->form([
                    Select::make('report_type')
                        ->label('Tipe Laporan')
                        ->options([
                            'daily' => 'Harian',
                            'monthly' => 'Bulanan',
                            'yearly' => 'Tahunan'
                        ])
                        ->required()
                        ->live(),
                    Select::make('period')
                        ->label('Periode')
                        ->options(function (Get $get) {
                            $type = $get('report_type');
                            return match ($type) {
                                'daily' => $this->getDailyOptions(),
                                'monthly' => $this->getMonthlyOptions(),
                                'yearly' => $this->getYearlyOptions(),
                                default => [],
                            };
                        })
                        ->required()
                        ->hidden(fn (Get $get) => !$get('report_type'))
                ])
                ->action(function (array $data) {
                    return redirect()->route('invoice.report', [
                        'type' => $data['report_type'],
                        'period' => $data['period']
                    ]);
                })
        ];
    }

    protected function getDailyOptions()
    {
        return InvoiceView::selectRaw('DATE(invoice_date) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->pluck('date', 'date')
            ->toArray();
    }

    protected function getMonthlyOptions()
    {
        return InvoiceView::selectRaw('DATE_FORMAT(invoice_date, "%Y-%m") as month')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->pluck('month', 'month')
            ->toArray();
    }

    protected function getYearlyOptions()
    {
        return InvoiceView::selectRaw('YEAR(invoice_date) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year', 'year')
            ->toArray();
    }
}
