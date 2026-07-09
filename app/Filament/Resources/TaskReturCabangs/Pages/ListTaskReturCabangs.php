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
                    DB::transaction(function () use ($data) {
                        $type = 'retur_cabang';
                        $idTask = TaskIdGenerator::generate($type);
                        $startBaris = TaskIdGenerator::getNextBaris($type);

                        foreach ($data['tasks'] as $index => $taskData) {
                            $taskData['user_id'] = auth()->id();
                            $taskData['no_baris'] = $startBaris + $index;
                            $taskData['id_task'] = $idTask;
                            $this->getModel()::create($taskData);
                        }
                    });
                }),
        ];
    }
}
