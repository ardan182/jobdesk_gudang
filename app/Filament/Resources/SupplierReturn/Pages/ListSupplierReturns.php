<?php

namespace App\Filament\Resources\SupplierReturn\Pages;

use App\Filament\Resources\SupplierReturn\Schemas\SupplierReturnForm;
use App\Filament\Resources\SupplierReturn\SupplierReturnResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListSupplierReturns extends ListRecords
{
    protected static string $resource = SupplierReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Retur Supplier')
                ->modalWidth(Width::Full)
                ->form(SupplierReturnForm::getFormFields())
                ->action(function (array $data) {
                    $data['id_task'] = TaskIdGenerator::generate('retur_supplier');
                    $data['user_id'] = auth()->id();
                    $this->getModel()::create($data);
                }),
        ];
    }
}
