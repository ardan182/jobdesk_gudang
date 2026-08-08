<?php

namespace App\Models\Concerns;

use App\Services\ActivityLogger;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function (self $model) {
            if (! static::shouldLogActivity($model, 'create')) {
                return;
            }
            ActivityLogger::created(
                $model,
                static::activityModule(),
                static::activitySummary($model),
                static::activityReferenceValue($model),
            );
        });

        static::updated(function (self $model) {
            if (! static::shouldLogActivity($model, 'update')) {
                return;
            }
            ActivityLogger::updated(
                $model,
                static::activityModule(),
                ActivityLogger::changes($model, static::activityTrackedFields()),
                static::activityReferenceValue($model),
            );
        });

        static::deleted(function (self $model) {
            if (! static::shouldLogActivity($model, 'delete')) {
                return;
            }
            ActivityLogger::deleted(
                $model,
                static::activityModule(),
                static::activitySummary($model),
                static::activityReferenceValue($model),
            );
        });
    }

    /**
     * Key modul konsisten (label dibaca via ActivityLogger::moduleLabel).
     */
    protected static function activityModule(): string
    {
        return 'general';
    }

    /**
     * Field yang didiff pada update. null = fillable (minus id/timestamp/FK).
     */
    protected static function activityTracked(): ?array
    {
        return null;
    }

    /**
     * Attribute ringkasan untuk aksi create/delete (ex: ['cabang','no_plat_mobil']).
     */
    protected static function activitySummaryAttributes(): array
    {
        return [];
    }

    /**
     * Field yang nilainya dipakai sebagai kolom reference.
     */
    protected static function activityReferenceField(): ?string
    {
        return null;
    }

    /**
     * Boleh dioverride di model untuk mengecualikan record buatan sistem.
     */
    protected static function shouldLogActivity($model, string $action): bool
    {
        return true;
    }

    protected static function activityTrackedFields(): array
    {
        if (($tracked = static::activityTracked()) !== null) {
            return $tracked;
        }

        $fallback = ['id_task', 'user_id', 'created_at', 'updated_at'];
        $foreign = ['supplier_id', 'expedition_id', 'arrival_supplier_truck_id', 'branch_shipment_id', 'task_keluar_barang_id', 'keluar_barang_id', 'warehouse_employee_id', 'master_kendaraan_id', 'division_id', 'master_supplier_id'];

        return array_values(array_diff(
            (new static)->getFillable() ?: [],
            array_merge($fallback, $foreign)
        ));
    }

    protected static function activityReferenceValue($model): ?string
    {
        $field = static::activityReferenceField();
        if (!$field) {
            return null;
        }
        $value = $model->$field ?? null;
        return blank($value) ? null : (string) $value;
    }

    protected static function activitySummary($model): string
    {
        $attrs = static::activitySummaryAttributes();

        $parts = [];
        foreach ($attrs as $attr) {
            $value = $model->$attr ?? null;
            if ($value === null || $value === '' || is_array($value)) {
                continue;
            }
            $parts[] = ActivityLogger::fieldLabel($attr) . ': ' . $value;
        }

        $summary = implode('; ', $parts);
        if ($summary !== '') {
            return $summary;
        }

        return $model->id_task ?? (string) $model->getKey();
    }
}