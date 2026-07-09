<?php

namespace App\Filament\Resources\TaskTerimaSuppliers\Pages;

use App\Filament\Resources\TaskTerimaSuppliers\Schemas\TaskTerimaSupplierForm;
use App\Filament\Resources\TaskTerimaSuppliers\TaskTerimaSupplierResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class ListTaskTerimaSuppliers extends ListRecords
{
    protected static string $resource = TaskTerimaSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Tambah')
                ->modalHeading('Tambah Task Terima Supplier')
                ->modalWidth(Width::Full)
                ->form([
                    Repeater::make('tasks')
                        ->schema(TaskTerimaSupplierForm::getFormFields())
                        ->table([
                            TableColumn::make('Supplier / Ekspedisi'),
                            TableColumn::make('No PO'),
                            TableColumn::make('Kolian'),
                            TableColumn::make('Jam Bongkar'),
                            TableColumn::make('Sopir'),
                            TableColumn::make('Status'),
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
                        $type = 'terima_supplier';
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
