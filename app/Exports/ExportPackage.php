<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportPackage implements FromCollection, WithHeadings
{
    protected $packages;

    public function __construct($packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->packages)->map(function ($package) {
            return [
                'package_name' => $package['package_name'],
                'uniqueId' => $package['uniqueId'],
                'origin' => $package['origin'],
                'destination' => $package['destination'],
                'receiver_name' => $package['receiver_name'],
                'sender_name' => $package['sender_name'],
                'price' => $package['price'],
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Package Name',
            'Unique ID',
            'Origin',
            'Destination',
            'Receiver Name',
            'Sender Name',
            'Price',
        ];
    }
}