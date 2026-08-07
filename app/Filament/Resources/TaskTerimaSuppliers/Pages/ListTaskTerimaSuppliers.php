<?php

namespace App\Filament\Resources\TaskTerimaSuppliers\Pages;

use App\Filament\Resources\TaskTerimaSuppliers\Schemas\TaskTerimaSupplierForm;
use App\Filament\Resources\TaskTerimaSuppliers\TaskTerimaSupplierResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListTaskTerimaSuppliers extends ListRecords
{
    protected static string $resource = TaskTerimaSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->visible(fn () => auth()->user()?->can('create_task_terima_suppliers') ?? false)
                ->label('Tambah')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Task Terima Supplier')
                ->modalWidth(Width::SevenExtraLarge)
                ->form(TaskTerimaSupplierForm::getFormFields())
                ->action(function (array $data) {
                    $helpers = $data['helpers'] ?? [];
                    unset($data['helpers'], $data['jenis_kiriman_tampil']);

                    $data['id_task'] = TaskIdGenerator::generate('terima_supplier');
                    $data['user_id'] = auth()->id();

                    $record = $this->getModel()::create($data);
                    if (filled($helpers)) {
                        $record->helpers()->sync($helpers);
                    }

                    Notification::make()
                        ->title('Task Terima Supplier berhasil disimpan')
                        ->success()
                        ->send();
                }),
        ];
    }
}
