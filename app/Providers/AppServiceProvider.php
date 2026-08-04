<?php

namespace App\Providers;

use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table->defaultPaginationPageOption(50);
            $table->paginationPageOptions([50, 100, 200, 'all']);
            $table->recordActionsPosition(RecordActionsPosition::BeforeColumns);
        }, isImportant: true);
    }
}
