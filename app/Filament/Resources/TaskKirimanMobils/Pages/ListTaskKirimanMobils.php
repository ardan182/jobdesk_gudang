<?php

namespace App\Filament\Resources\TaskKirimanMobils\Pages;

use App\Filament\Resources\TaskKirimanMobils\Schemas\TaskKirimanMobilForm;
use App\Filament\Resources\TaskKirimanMobils\TaskKirimanMobilResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class ListTaskKirimanMobils extends ListRecords
{
    protected static string $resource = TaskKirimanMobilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Tambah')
                ->color('primary')
                ->modalHeading('Tambah Task Kiriman Mobil')
                ->modalWidth(Width::Full)
                ->form([
                    Repeater::make('tasks')
                        ->schema(TaskKirimanMobilForm::getFormFields())
                        ->table([
                            TableColumn::make('Cabang'),
                            TableColumn::make('No Plat'),
                            TableColumn::make('Jam Muat'),
                            TableColumn::make('Jam Selesai'),
                            TableColumn::make('Jam Berangkat'),
                            TableColumn::make('Supir'),
                            TableColumn::make('Keterangan'),
                        ])
                        ->addActionAlignment(Alignment::End)
                        ->label('Daftar Task')
                        ->default([[]])
                        ->reorderable(false)
                        ->addActionLabel('Tambah Baris'),
                ])
                ->action(function (array $data) {
                    $ids = [];
                    foreach ($data['tasks'] as $i => $task) {
                        $ids[$i] = TaskIdGenerator::generate('kiriman_mobil');
                    }

                    DB::transaction(function () use ($data, $ids) {
                        foreach ($data['tasks'] as $i => $taskData) {
                            $taskData['user_id'] = auth()->id();
                            $taskData['id_task'] = $ids[$i];
                            $this->getModel()::create($taskData);
                        }
                    });
                }),
        ];
    }
}
