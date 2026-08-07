<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false)
                ->color("primary")
                ->icon('heroicon-m-plus')
                ->modalHeading('Buat User')
                ->modalDescription('Buat akun baru dan atur hak akses.')
                ->modalSubmitActionLabel('Buat')
                ->modalWidth(Width::Full)
                ->createAnother(false)
                ->using(function (array $data): Model {
                    $user = new User;
                    $user->fill($data);
                    $user->save();

                    $this->syncUserPermissions($user, $data);

                    return $user;
                }),
        ];
    }

    protected function syncUserPermissions(User $user, array $data): void
    {
        $permissions = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'perm_') && $value) {
                $permissions[] = str_replace('perm_', '', $key);
            }
        }
        $user->syncPermissions($permissions);
    }
}
