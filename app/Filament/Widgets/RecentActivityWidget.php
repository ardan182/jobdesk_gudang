<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = '⚡ Aktivitas Terakhir';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 100;

    public function table(Table $table): Table
    {
        return $table
            ->query(ActivityLog::query()->with('user'))
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Aktivitas')
                    ->searchable()
                    ->limit(120),
                TextColumn::make('reference')
                    ->label('Refferensi')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('module')
                    ->label('Modul')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Retur Supplier' => 'warning',
                        'Retur Cabang' => 'info',
                        'Terima Supplier' => 'success',
                        'Keluar Barang' => 'danger',
                        'Kiriman Mobil' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('module')
                    ->options([
                        'Retur Supplier' => 'Retur Supplier',
                        'Retur Cabang' => 'Retur Cabang',
                        'Terima Supplier' => 'Terima Supplier',
                        'Keluar Barang' => 'Keluar Barang',
                        'Kiriman Mobil' => 'Kiriman Mobil',
                    ]),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return $user->hasAnyRole(['Admin', 'Checker Retur', 'Checker Terima', 'Checker Keluar', 'Checker Kiriman']);
    }
}
