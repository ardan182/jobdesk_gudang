<?php

namespace App\Providers;

use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });

        Table::configureUsing(function (Table $table): void {
            $table->defaultPaginationPageOption(50);
            $table->paginationPageOptions([50, 100, 200, 'all']);
            $table->recordActionsPosition(RecordActionsPosition::BeforeColumns);
        }, isImportant: true);
    }
}
