<?php

namespace App\Filament\Resources\TaskReturCabangs\Pages;

use App\Filament\Resources\TaskReturCabangs\Schemas\TaskReturCabangForm;
use App\Filament\Resources\TaskReturCabangs\TaskReturCabangResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListTaskReturCabangs extends ListRecords
{
    protected static string $resource = TaskReturCabangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Tambah')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Retur Masuk dari Cabang')
                ->modalWidth(Width::Full)
                ->form(TaskReturCabangForm::getFormFields())
                ->action(function (array $data) {
                    $helpers = $data['helpers'] ?? [];
                    unset($data['helpers'], $data['kiriman_mobil_id']);
                    $data['id_task'] = TaskIdGenerator::generate('retur_cabang');
                    $data['user_id'] = auth()->id();
                    $record = $this->getModel()::create($data);
                    if (filled($helpers)) {
                        $record->helpers()->sync($helpers);
                    }
                }),
        ];
    }
}
