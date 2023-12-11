<?php

namespace App\Exports;

use App\Models\Inventory;
use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithEvents, ShouldAutoSize, WithCustomStartCell
{

    private $warehouse_id;
    private $warehouse_name;

    public function __construct($warehouse_id)
    {
        $this->warehouse_name = Warehouse::find($warehouse_id)->name;
        $this->warehouse_id = $warehouse_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Inventory::where('warehouse_id', $this->warehouse_id)->get();
    }

    public function map($inventory): array
    {
        return [
            $inventory->title,
            $inventory->code,
            Inventory::TYPE[$inventory->type],
            (string) $inventory->initial_count,
            (string) $inventory->current_count,
            (string) $inventory->getInputCount(),
            (string) $inventory->getOutputCount(),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setRightToLeft(true)
                    ->getStyle('A1:XFD1048576')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A1:XFD1048576')->getFont()->setName('B Nazanin');

                $event->sheet->mergeCells('A1:G1')->setCellValue('A1',$this->warehouse_name);
            },
        ];
    }

    public function headings(): array
    {
        return [
            'A' => 'عنوان کالا',
            'B' => 'کد کالا',
            'C' => 'نوع کالا',
            'D' => 'موجودی اولیه',
            'E' => 'موجودی فعلی',
            'F' => 'تعداد ورود',
            'G' => 'تعداد خروج',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5d4a9c']
            ]
        ])->getFont()->setColor(Color::indexedColor(2));

        $sheet->getStyle('A2:G2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'bfabff']
            ]
        ])->getFont()->setColor(Color::indexedColor(2));

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
}
