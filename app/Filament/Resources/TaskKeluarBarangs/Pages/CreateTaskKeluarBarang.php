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
            $ids = [];
            foreach ($data['tasks'] as $i => $task) {
                $ids[$i] = TaskIdGenerator::generate('keluar_barang');
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
