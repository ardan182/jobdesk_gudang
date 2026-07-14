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
                ->color('primary')
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
                    $ids = [];
                    foreach ($data['tasks'] as $i => $task) {
                        $ids[$i] = TaskIdGenerator::generate('terima_supplier');
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
