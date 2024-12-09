<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Packet;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithEvents, ShouldAutoSize, WithColumnFormatting
{

    public function collection()
    {
        return Supplier::all();
    }

    public function map($suppliers): array
    {
        return [
            $suppliers->name,
            $suppliers->code ?? '---',
            $suppliers->economical_number ?? '---',
            $suppliers->national_number ?? '---',
            $suppliers->postal_code ?? '---',
            $suppliers->province ?? '---',
            $suppliers->city ?? '---',
            $suppliers->phone1 ?? '---',
            $suppliers->address1 ?? '---',
            $suppliers->description ?? '---',
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
            },
        ];
    }

    public function headings(): array
    {
        return [
            'A' => 'نام حقیقی/حقوقی',
            'B' => 'کد',
            'C' => 'شماره اقتصادی',
            'D' => 'شماره ثبت/ملی',
            'E' => 'کد پستی',
            'F' => 'استان',
            'G' => 'شهر',
            'H' => 'شماره تماس',
            'I' => 'آدرس',
            'J' => 'توضیحات',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5d4a9c']
            ]
        ])->getFont()->setColor(Color::indexedColor(2));

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
