<?php

namespace App\Filament\Resources\TaskReturSuppliers\Pages;

use App\Filament\Resources\TaskReturSuppliers\TaskReturSupplierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaskReturSupplier extends EditRecord
{
    protected static string $resource = TaskReturSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
        ];
    }
}
