<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $permissions = [];
        foreach ($this->data as $key => $value) {
            if (str_starts_with($key, 'perm_') && $value) {
                $permissions[] = str_replace('perm_', '', $key);
            }
        }
        $this->record->syncPermissions($permissions);
    }
}
