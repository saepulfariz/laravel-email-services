<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Username',
            'Email',
            'Status',
            'Roles',
            'Joined At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->username ? '@' . $user->username : '-',
            $user->email,
            $user->is_active ? 'Active' : 'Inactive',
            $user->roles->pluck('name')->join(', '),
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
