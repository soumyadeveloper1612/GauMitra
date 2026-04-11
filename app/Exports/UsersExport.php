<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Mobile',
            'Status',
            'Verified',
            'Verified At',
            'Last Login',
            'Address Count',
            'Full Address',
            'Street',
            'Village',
            'Police Station',
            'City',
            'District',
            'State',
            'Pincode',
            'Area Name',
            'Latitude',
            'Longitude',
            'Google Place ID',
        ];
    }

    public function map($user): array
    {
        $address = $user->latestAddress;

        return [
            $user->id,
            $user->name ?? '',
            $user->mobile ?? '',
            $user->status ?? '',
            $user->mobile_verified_at ? 'Yes' : 'No',
            $user->mobile_verified_at ? $user->mobile_verified_at->format('d-m-Y h:i A') : '',
            $user->last_login_at ? $user->last_login_at->format('d-m-Y h:i A') : '',
            $user->addresses_count ?? 0,
            $address->full_address ?? '',
            $address->street ?? '',
            $address->village ?? '',
            $address->police_station ?? '',
            $address->city ?? '',
            $address->district ?? '',
            $address->state ?? '',
            $address->pincode ?? '',
            $address->area_name ?? '',
            $address->latitude ?? '',
            $address->longitude ?? '',
            $address->google_place_id ?? '',
        ];
    }
}