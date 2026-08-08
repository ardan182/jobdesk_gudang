<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Expedition extends Model
{
    use LogsActivity;

    protected $table = 'expeditions';

    protected $fillable = [
        'nama_ekspedisi',
        'no_telepon',
        'alamat',
    ];

    protected static function activityModule(): string { return 'expeditions'; } protected static function activitySummaryAttributes(): array { return ['nama_ekspedisi']; } protected static function activityReferenceField(): ?string { return 'nama_ekspedisi'; } }
