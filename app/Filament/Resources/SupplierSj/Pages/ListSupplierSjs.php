<?php

namespace App\Filament\Resources\SupplierSj\Pages;

use App\Filament\Resources\SupplierSj\SupplierSjResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSupplierSjs extends ListRecords
{
    protected static string $resource = SupplierSjResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Input SJ Baru')
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->modalHeading('Input SJ Baru')
                ->modalWidth('lg')
                ->form(\App\Filament\Resources\SupplierSj\Schemas\SupplierSjForm::getFormFields())
                ->action(function (array $data) {
                    $this->getModel()::create($data);
                }),
        ];
    }
}
