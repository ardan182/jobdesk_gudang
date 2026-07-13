<?php

namespace App\Filament\Resources\TaskReturCabangs\Pages;

use App\Filament\Resources\TaskReturCabangs\Schemas\TaskReturCabangForm;
use App\Filament\Resources\TaskReturCabangs\TaskReturCabangResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class ListTaskReturCabangs extends ListRecords
{
    protected static string $resource = TaskReturCabangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Tambah')
                ->modalHeading('Tambah Task Retur Cabang')
                ->modalWidth(Width::Full)
                ->form([
                    Repeater::make('tasks')
                        ->schema(TaskReturCabangForm::getFormFields())
                        ->table([
                            TableColumn::make('Cabang'),
                            TableColumn::make('Jenis Retur'),
                            TableColumn::make('No SJ'),
                            TableColumn::make('Total Kolian'),
                            TableColumn::make('Jam Bongkar'),
                            TableColumn::make('Sopir'),
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
                        $ids[$i] = TaskIdGenerator::generate('retur_cabang');
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
