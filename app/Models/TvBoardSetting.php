<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TvBoardSetting extends Model
{
    protected $fillable = [
        'refresh_interval',
        'max_items',
        'show_supplier_arrivals',
        'show_branch_deliveries',
        'show_shipment_sj',
        'show_supplier_invoices',
        'marquee_message',
    ];

    protected function casts(): array
    {
        return [
            'refresh_interval' => 'integer',
            'max_items' => 'integer',
            'show_supplier_arrivals' => 'boolean',
            'show_branch_deliveries' => 'boolean',
            'show_shipment_sj' => 'boolean',
            'show_supplier_invoices' => 'boolean',
        ];
    }
}
