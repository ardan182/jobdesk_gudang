<?php

namespace App\Filament\Resources\TaskKirimanMobils\Pages;

use App\Filament\Resources\TaskKirimanMobils\TaskKirimanMobilResource;
use App\Services\TaskIdGenerator;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTaskKirimanMobil extends CreateRecord
{
    protected static string $resource = TaskKirimanMobilResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function create(bool $another = false): void
    {
        $data = $this->form->getState();

        if (isset($data['tasks']) && is_array($data['tasks'])) {
            DB::transaction(function () use ($data) {
                $type = 'kiriman_mobil';
                $startBaris = TaskIdGenerator::getNextBaris($type);
                $lastIdNumber = TaskIdGenerator::getLastIdNumber($type);

                foreach ($data['tasks'] as $index => $taskData) {
                    $taskData['user_id'] = auth()->id();
                    $taskData['no_baris'] = $startBaris + $index;
                    $taskData['id_task'] = TaskIdGenerator::formatId($type, $lastIdNumber + $index + 1);
                    $this->getModel()::create($taskData);
                }
            });
        }

        $this->redirect($this->getRedirectUrl());
    }
}
