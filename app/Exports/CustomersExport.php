<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Packet;
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

class CustomersExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithEvents, ShouldAutoSize, WithColumnFormatting
{

    public function collection()
    {
        return Customer::all();
    }

    public function map($customer): array
    {
        return [
            $customer->name,
            $customer->code ?? '---',
            Customer::TYPE[$customer->type],
            Customer::CUSTOMER_TYPE[$customer->customer_type],
            $customer->economical_number ?? '---',
            $customer->national_number ?? '---',
            $customer->postal_code ?? '---',
            $customer->province ?? '---',
            $customer->city ?? '---',
            $customer->phone1 ?? '---',
            $customer->address1 ?? '---',
            $customer->description ?? '---',
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
            'C' => 'نوع',
            'D' => 'مشتری',
            'E' => 'شماره اقتصادی',
            'F' => 'شماره ثبت/ملی',
            'G' => 'کد پستی',
            'H' => 'استان',
            'I' => 'شهر',
            'J' => 'شماره تماس',
            'K' => 'آدرس',
            'L' => 'توضیحات',
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
