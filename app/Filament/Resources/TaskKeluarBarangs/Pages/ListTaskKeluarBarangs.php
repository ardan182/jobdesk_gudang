<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Pages;

use App\Filament\Resources\TaskKeluarBarangs\Schemas\TaskKeluarBarangForm;
use App\Filament\Resources\TaskKeluarBarangs\TaskKeluarBarangResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class ListTaskKeluarBarangs extends ListRecords
{
    protected static string $resource = TaskKeluarBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Tambah')
                ->modalHeading('Tambah Task Keluar Barang')
                ->modalWidth(Width::Full)
                ->form([
                    Repeater::make('tasks')
                        ->schema(TaskKeluarBarangForm::getFormFields())
                        ->table([
                            TableColumn::make('Toko Tujuan'),
                            TableColumn::make('Supplier'),
                            TableColumn::make('No SJ'),
                            TableColumn::make('Kolian'),
                            TableColumn::make('Jam Naik'),
                            TableColumn::make('Koordinator'),
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
                }),
        ];
    }
}
