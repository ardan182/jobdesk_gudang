<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterSave(): void
    {
        $permissions = [];
        foreach ($this->data as $key => $value) {
            if (str_starts_with($key, 'perm_') && $value) {
                $permissions[] = str_replace('perm_', '', $key);
            }
        }

        $this->record->is_super_admin = (bool) ($this->data['is_super_admin'] ?? false);
        $this->record->permission_template = json_encode(array_values(array_unique($permissions)));
        $this->record->save();
    }
}
