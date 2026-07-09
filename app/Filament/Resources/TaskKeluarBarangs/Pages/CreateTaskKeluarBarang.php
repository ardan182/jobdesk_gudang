<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Pages;

use App\Filament\Resources\TaskKeluarBarangs\TaskKeluarBarangResource;
use App\Services\TaskIdGenerator;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTaskKeluarBarang extends CreateRecord
{
    protected static string $resource = TaskKeluarBarangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function create(bool $another = false): void
    {
        $data = $this->form->getState();

        if (isset($data['tasks']) && is_array($data['tasks'])) {
            DB::transaction(function () use ($data) {
                $type = 'keluar_barang';
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
