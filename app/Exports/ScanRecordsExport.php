<?php

namespace App\Exports;

use App\Models\ScanRecord;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ScanRecordsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithMultipleSheets,
    WithTitle
{
    public function __construct(
        protected string $dateFrom,
        protected string $dateTo,
        protected ?int $userId = null,
        protected ?int $outletId = null,
        protected ?string $outletType = null,
        protected array $allowedOutletIds = [],
    ) {}


    public function title(): string
    {
        return 'Detail Scan';
    }

    /*
    |--------------------------------------------------------------------------
    | SHEETS
    |--------------------------------------------------------------------------
    */

    public function sheets(): array
    {
        return [
            new ScanRecordsDetailExport(
                $this->dateFrom,
                $this->dateTo,
                $this->userId,
                $this->outletId,
                $this->outletType,
                $this->allowedOutletIds,
            ),

            new ScanOutletSummaryExport(
                $this->dateFrom,
                $this->dateTo,
                $this->userId,
                $this->outletId,
                $this->outletType,
                $this->allowedOutletIds,
            ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    |
    | Tetap dipertahankan supaya struktur export lama tidak hilang.
    |
    */

    public function query(): Builder
    {
        $query = ScanRecord::query()
            ->with([
                'user',
                'outlet',
            ])
            ->whereDate(
                'scanned_at',
                '>=',
                $this->dateFrom
            )
            ->whereDate(
                'scanned_at',
                '<=',
                $this->dateTo
            );

        if (!empty($this->allowedOutletIds)) {
            $query->whereIn(
                'outlet_id',
                $this->allowedOutletIds
            );
        }

        if ($this->userId !== null) {
            $query->where(
                'user_id',
                $this->userId
            );
        }

        if ($this->outletId !== null) {
            $query->where(
                'outlet_id',
                $this->outletId
            );
        }

        if (
            $this->outletType !== null &&
            $this->outletType !== ''
        ) {
            $query->whereHas(
                'outlet',
                function (Builder $outletQuery) {
                    $outletQuery->where(
                        'outlet_type',
                        $this->outletType
                    );
                }
            );
        }

        return $query->orderByDesc('scanned_at');
    }

    public function headings(): array
    {
        return [
            'No Tiket',
            'QR Code',
            'Ticket Type',
            'Operator',
            'Outlet',
            'Outlet Type',
            'Method',
            'Scanned At',
        ];
    }

    public function map($scan): array
    {
        return [
            $scan->no_tiket ?? '-',
            $scan->qrcode ?? '-',
            $scan->ticket_type ?? '-',
            $scan->user?->name ?? '-',
            $scan->outlet?->outlet_name ?? '-',
            $scan->outlet?->outlet_type ?? '-',
            ucfirst($scan->scan_method ?? '-'),
            $scan->scanned_at
                ? $scan->scanned_at->format('d-m-Y H:i:s')
                : '-',
        ];
    }
}
