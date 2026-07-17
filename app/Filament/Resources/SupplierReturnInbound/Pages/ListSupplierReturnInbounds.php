<?php

namespace App\Filament\Resources\SupplierReturnInbound\Pages;

use App\Filament\Resources\SupplierReturnInbound\Schemas\SupplierReturnInboundForm;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSupplierReturnInbounds extends ListRecords
{
    protected static string $resource = \App\Filament\Resources\SupplierReturnInbound\SupplierReturnInboundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Retur Masuk')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Retur Masuk dari Supplier')
                ->modalWidth('lg')
                ->form(SupplierReturnInboundForm::getFormFields())
                ->action(function (array $data) {
                    $this->getModel()::create($data);
                }),
        ];
    }
}
