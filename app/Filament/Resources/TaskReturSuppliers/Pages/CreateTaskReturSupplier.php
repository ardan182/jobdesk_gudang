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
            $ids = [];
            foreach ($data['tasks'] as $i => $task) {
                $ids[$i] = TaskIdGenerator::generate('retur_supplier');
            }

            DB::transaction(function () use ($data, $ids) {
                foreach ($data['tasks'] as $i => $taskData) {
                    $taskData['user_id'] = auth()->id();
                    $taskData['id_task'] = $ids[$i];
                    $this->getModel()::create($taskData);
                }
            });
        }

        $this->redirect($this->getRedirectUrl());
    }
}
