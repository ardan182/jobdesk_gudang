<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class MasterToko extends Model
{
    use LogsActivity;

    protected $table = 'master_tokos';

    protected $fillable = [
        'nama_toko',
        'alamat',
    ];

    protected static function activityModule(): string { return 'master_tokos'; } protected static function activitySummaryAttributes(): array { return ['nama_toko']; } protected static function activityReferenceField(): ?string { return 'nama_toko'; } }
