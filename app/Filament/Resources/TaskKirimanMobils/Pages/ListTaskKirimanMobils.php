<?php

namespace App\Filament\Resources\TaskKirimanMobils\Pages;

use App\Filament\Resources\TaskKirimanMobils\Schemas\TaskKirimanMobilForm;
use App\Filament\Resources\TaskKirimanMobils\TaskKirimanMobilResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListTaskKirimanMobils extends ListRecords
{
    protected static string $resource = TaskKirimanMobilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Kiriman Mobil')
                ->modalWidth(Width::Full)
                ->form(TaskKirimanMobilForm::getFormFields())
                ->action(function (array $data) {
                    $sjs = $data['branch_shipments'] ?? [];
                    unset(
                        $data['branch_shipments'],
                        $data['total_sj_tampil'],
                        $data['sisa_sj_tampil'],
                        $data['durasi_tampil'],
                    );
                    $data['id_task'] = TaskIdGenerator::generate('kiriman_mobil');
                    $data['user_id'] = auth()->id();
                    $record = $this->getModel()::create($data);

                    if (filled($sjs)) {
                        $record->branchShipments()->sync($sjs);
                    }
                }),
        ];
    }
}
