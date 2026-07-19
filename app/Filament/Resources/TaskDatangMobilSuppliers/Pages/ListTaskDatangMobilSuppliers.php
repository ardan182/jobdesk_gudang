<?php

namespace App\Filament\Resources\TaskDatangMobilSuppliers\Pages;

use App\Filament\Resources\TaskDatangMobilSuppliers\Schemas\TaskDatangMobilSupplierForm;
use App\Filament\Resources\TaskDatangMobilSuppliers\TaskDatangMobilSupplierResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTaskDatangMobilSuppliers extends ListRecords
{
    protected static string $resource = TaskDatangMobilSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Datang Mobil Supplier')
                ->modalWidth('xl')
                ->form(TaskDatangMobilSupplierForm::getFormFields())
                ->action(function (array $data) {
                    $data['id_task'] = TaskIdGenerator::generate('datang_mobil_supplier');
                    $data['user_id'] = auth()->id();
                    $this->getModel()::create($data);
                }),
        ];
    }
}
