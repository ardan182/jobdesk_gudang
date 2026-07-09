<?php

namespace App\Filament\Resources\TaskReturSuppliers\Pages;

use App\Filament\Resources\TaskReturSuppliers\TaskReturSupplierResource;
use App\Services\TaskIdGenerator;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTaskReturSupplier extends CreateRecord
{
    protected static string $resource = TaskReturSupplierResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function create(bool $another = false): void
    {
        $data = $this->form->getState();

        if (isset($data['tasks']) && is_array($data['tasks'])) {
            DB::transaction(function () use ($data) {
                $type = 'retur_supplier';
                $idTask = TaskIdGenerator::generate($type);
                $startBaris = TaskIdGenerator::getNextBaris($type);

                foreach ($data['tasks'] as $index => $taskData) {
                    $taskData['user_id'] = auth()->id();
                    $taskData['no_baris'] = $startBaris + $index;
                    $taskData['id_task'] = $idTask;
                    $this->getModel()::create($taskData);
                }
            });
        }

        $this->redirect($this->getRedirectUrl());
    }
}
