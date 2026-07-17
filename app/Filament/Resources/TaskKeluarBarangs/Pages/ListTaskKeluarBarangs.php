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
                ->color('primary')
                ->icon('heroicon-m-plus')
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
                }),
        ];
    }
}
