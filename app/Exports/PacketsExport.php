<?php

namespace App\Exports;

use App\Models\Packet;
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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PacketsExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Packet::all();
    }

    public function map($packet): array
    {
        return [
            $packet->receiver,
            $packet->invoice_id,
            $packet->address,
            verta($packet->sent_time)->format('Y/m/d'),
            Packet::SENT_TYPE[$packet->sent_type],
            $packet->send_tracking_code ?? '---',
            $packet->receive_tracking_code ?? '---',
            Packet::PACKET_STATUS[$packet->packet_status],
            Packet::INVOICE_STATUS[$packet->invoice_status],
            $packet->description ?? '---',
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
            'A' => 'گیرنده',
            'B' => 'شماره سفارش',
            'C' => 'آدرس',
            'D' => 'زمان ارسال',
            'E' => 'نوع ارسال',
            'F' => 'کد رهگیری ارسالی',
            'G' => 'کد رهگیری دریافتی',
            'H' => 'وضعیت بسته',
            'I' => 'وضعیت فاکتور',
            'J' => 'توضیحات',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
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
