<?php

namespace App\Filament\Resources\KomplainPo\Pages;

use App\Filament\Resources\KomplainPo\Schemas\KomplainPoForm;
use App\Filament\Resources\KomplainPo\KomplainPoResource;
use App\Services\TaskIdGenerator;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListKomplainPos extends ListRecords
{
    protected static string $resource = KomplainPoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->visible(fn () => auth()->user()?->can('create_komplain_pos') ?? false)
                ->label('Tambah Komplain PO')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Komplain PO')
                ->modalWidth(Width::SevenExtraLarge)
                ->form(KomplainPoForm::getFormFields())
                ->action(function (array $data) {
                    $data['id_task'] = TaskIdGenerator::generate('komplain_po');
                    $data['user_id'] = auth()->id();
                    $this->getModel()::create($data);
                }),
        ];
    }
}
