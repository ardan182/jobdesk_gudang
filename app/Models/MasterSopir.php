<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class MasterSopir extends Model
{
    use LogsActivity;

    protected $table = 'master_sopirs';

    protected $fillable = [
        'nama_sopir',
        'no_whatsapp',
    ];

    protected static function activityModule(): string { return 'master_sopirs'; } protected static function activitySummaryAttributes(): array { return ['nama_sopir']; } protected static function activityReferenceField(): ?string { return 'nama_sopir'; } }
