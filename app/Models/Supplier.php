<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use LogsActivity;

    protected $table = 'suppliers';

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'no_telepon',
        'keterangan',
    ];

    protected static function activityModule(): string { return 'master_suppliers'; } protected static function activitySummaryAttributes(): array { return ['nama_supplier']; } protected static function activityReferenceField(): ?string { return 'kode_supplier'; } }
