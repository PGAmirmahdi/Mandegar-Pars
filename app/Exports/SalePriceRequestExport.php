<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\SalePriceRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalePriceRequestExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return SalePriceRequest::all();
    }
    public function map($salePriceRequest): array
    {
        return [
            $salePriceRequest->id,
            optional($salePriceRequest->user)->fullname(),
            optional($salePriceRequest->acceptor)->fullname(),
            optional($salePriceRequest->customer)->name,
            $this->getTotalPrice($salePriceRequest->products),
            $this->getPaymentType($salePriceRequest->payment_type), // نوع پرداختی فارسی$this->getPaymentType($salePriceRequest->payment_type), // نوع پرداختی فارسی
            $salePriceRequest->status ? SalePriceRequest::STATUS[$salePriceRequest->status] : 'نامشخص',
            $this->formatProducts($salePriceRequest->products),
            $salePriceRequest->code,
            SalePriceRequest::TYPE[$salePriceRequest->type] ?? 'نامشخص',
            $salePriceRequest->created_at->format('Y-m-d H:i:s'),
        ];
    }

    private function formatProducts($products)
    {
        if (is_null($products)) {
            return 'ندارد';
        }

        $decoded = json_decode($products, true);
        if (is_array($decoded)) {
            return implode(', ', array_column($decoded, 'product_name'));
        }

        return $products;
    }

    private function getTotalPrice($products)
    {
        $total = 0;
        $decoded = json_decode($products, true);

        if (is_array($decoded)) {
            foreach ($decoded as $product) {
                $total += $product['count'] * $product['price'];
            }
        }

        return number_format($total) . ' ریال';
    }
    public function headings(): array
    {
        return [
            'A'=>'ID',
            'B'=>'درخواست دهنده',
            'C'=>'تایید کننده',
            'D'=>'مشتری',
            'F'=>'قیمت کل کارشناس',
            'G'=>'نوع پرداختی',
            'H'=>'وضعیت',
            'I'=>'محصولات',
            'K'=>'کد درخواست',
            'L'=>'نوع فروش',
            'P'=>'تاریخ ایجاد',
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
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
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
            'F' => NumberFormat::FORMAT_NUMBER,
        ];
    }
    private function getPaymentType($paymentType)
    {
        return Order::Payment_Type[$paymentType] ?? 'نامشخص';
    }
}
