<?php

namespace App\Filament\Resources\TaskKirimanMobils\Pages;

use App\Filament\Resources\TaskKirimanMobils\TaskKirimanMobilResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaskKirimanMobils extends ListRecords
{
    protected static string $resource = TaskKirimanMobilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
