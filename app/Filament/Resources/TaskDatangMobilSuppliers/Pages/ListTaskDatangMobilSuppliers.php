<?php

namespace App\Filament\Resources\TaskDatangMobilSuppliers\Pages;

use App\Filament\Resources\TaskDatangMobilSuppliers\Schemas\TaskDatangMobilSupplierForm;
use App\Filament\Resources\TaskDatangMobilSuppliers\TaskDatangMobilSupplierResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class ListTaskDatangMobilSuppliers extends ListRecords
{
    protected static string $resource = TaskDatangMobilSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Tambah')
                ->color('primary')
                ->modalHeading('Tambah Datang Mobil Supplier')
                ->modalWidth(Width::Full)
                ->form([
                    Repeater::make('tasks')
                        ->schema(TaskDatangMobilSupplierForm::getFormFields())
                        ->table([
                            TableColumn::make('Supplier'),
                            TableColumn::make('Ekspedisi'),
                            TableColumn::make('Sopir'),
                            TableColumn::make('No Plat'),
                            TableColumn::make('Tgl Datang'),
                            TableColumn::make('Jam Datang'),
                            TableColumn::make('Jam Selesai'),
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
                        $ids[$i] = TaskIdGenerator::generate('datang_mobil_supplier');
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
