<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::all();
    }

    public function map($product): array
    {
        return [
            $product->title,
            $product->code,
            number_format($product->system_price),
            number_format($product->partner_price_tehran),
            number_format($product->partner_price_other),
            number_format($product->single_price),
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

//                $event->sheet->mergeCells('B1:C1');
            },
        ];
    }

    public function headings(): array
    {
        return [
            'A' => 'عنوان محصول',
            'B' => 'کد محصول',
            'C' => 'قیمت سامانه',
            'D' => 'قیمت همکار - تعران',
            'E' => 'قیمت همکار - شهرستان',
            'F' => 'قیمت تک فروشی',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5d4a9c']
            ]
        ])->getFont()->setColor(Color::indexedColor(2));

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
