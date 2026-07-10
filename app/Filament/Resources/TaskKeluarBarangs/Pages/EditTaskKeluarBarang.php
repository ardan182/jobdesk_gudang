<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Pages;

use App\Filament\Resources\TaskKeluarBarangs\TaskKeluarBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaskKeluarBarang extends EditRecord
{
    protected static string $resource = TaskKeluarBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
        ];
    }
}
