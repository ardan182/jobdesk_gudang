<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Pages;

use App\Filament\Resources\TaskKeluarBarangs\Schemas\TaskKeluarBarangForm;
use App\Filament\Resources\TaskKeluarBarangs\TaskKeluarBarangResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListTaskKeluarBarangs extends ListRecords
{
    protected static string $resource = TaskKeluarBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Task Keluar Barang')
                ->modalWidth(Width::Full)
                ->form(TaskKeluarBarangForm::getFormFields())
                ->action(function (array $data) {
                    $data['id_task'] = TaskIdGenerator::generate('keluar_barang');
                    $data['user_id'] = auth()->id();
                    $this->getModel()::create($data);
                }),
        ];
    }
}
