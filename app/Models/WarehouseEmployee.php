<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseEmployee extends Model
{
    use LogsActivity;

    protected $table = 'warehouse_employees';

    protected $fillable = [
        'nama_karyawan',
        'no_whatsapp',
        'division_id',
        'jatah_cuti',
    ];

    protected static function activityModule(): string { return 'warehouse_employees'; } protected static function activitySummaryAttributes(): array { return ['nama_karyawan']; } protected static function activityReferenceField(): ?string { return 'nama_karyawan'; } public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(WarehouseLeave::class, 'warehouse_employee_id');
    }
}
