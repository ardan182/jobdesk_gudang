<?php

namespace App\Filament\Resources\TaskReturSuppliers\Pages;

use App\Filament\Resources\TaskReturSuppliers\Schemas\TaskReturSupplierForm;
use App\Filament\Resources\TaskReturSuppliers\TaskReturSupplierResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class ListTaskReturSuppliers extends ListRecords
{
    protected static string $resource = TaskReturSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Tambah')
                ->color('primary')
                ->modalHeading('Tambah Task Retur Supplier')
                ->modalWidth(Width::Full)
                ->form([
                    Repeater::make('tasks')
                        ->schema(TaskReturSupplierForm::getFormFields())
                        ->table([
                            TableColumn::make('Supplier / Ekspedisi'),
                            TableColumn::make('No Plat'),
                            TableColumn::make('Sopir'),
                            TableColumn::make('Jam Muat'),
                            TableColumn::make('Kolian'),
                            TableColumn::make('Admin SJ'),
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
                        $ids[$i] = TaskIdGenerator::generate('retur_supplier');
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
