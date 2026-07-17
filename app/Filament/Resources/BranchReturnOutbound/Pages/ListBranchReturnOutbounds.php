<?php

namespace App\Filament\Resources\BranchReturnOutbound\Pages;

use App\Filament\Resources\BranchReturnOutbound\Schemas\BranchReturnOutboundForm;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListBranchReturnOutbounds extends ListRecords
{
    protected static string $resource = \App\Filament\Resources\BranchReturnOutbound\BranchReturnOutboundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Retur Keluar')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Retur Keluar untuk Cabang')
                ->modalWidth('lg')
                ->form(BranchReturnOutboundForm::getFormFields())
                ->action(function (array $data) {
                    $this->getModel()::create($data);
                }),
        ];
    }
}
