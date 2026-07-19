<?php

namespace App\Filament\Resources\TaskDatangMobilSuppliers\Pages;

use App\Filament\Resources\TaskDatangMobilSuppliers\TaskDatangMobilSupplierResource;
use App\Services\TaskIdGenerator;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskDatangMobilSupplier extends CreateRecord
{
    protected static string $resource = TaskDatangMobilSupplierResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function create(bool $another = false): void
    {
        $data = $this->form->getState();
        $data['id_task'] = TaskIdGenerator::generate('datang_mobil_supplier');
        $data['user_id'] = auth()->id();

        $this->getModel()::create($data);

        $this->redirect($this->getRedirectUrl());
    }
}
