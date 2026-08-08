<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = '⚡ Aktivitas Terakhir';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 100;

    protected function headerActionMap(): array
    {
        return [
            'create' => ['Dibuat', 'success', 'heroicon-m-plus-circle'],
            'update' => ['Diubah', 'warning', 'heroicon-m-pencil-square'],
            'delete' => ['Dihapus', 'danger', 'heroicon-m-trash'],
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ActivityLog::query()->with('user'))
            ->columns([
                TextColumn::make('action')
                    ->label('Aksi')
                    ->grow(false)
                    ->badge()
                    ->icon(fn (string $state) => $this->headerActionMap()[$state][2] ?? 'heroicon-m-clock')
                    ->color(fn (string $state) => $this->headerActionMap()[$state][1] ?? 'gray')
                    ->formatStateUsing(fn (string $state) => $this->headerActionMap()[$state][0] ?? $state),
                TextColumn::make('id_task')
                    ->label('ID')
                    ->badge()
                    ->color('gray')
                    ->grow(false)
                    ->searchable(),
                TextColumn::make('module')
                    ->label('Menu')
                    ->grow(false)
                    ->badge()
                    ->icon(fn (string $state) => $this->moduleIcon($state))
                    ->color(fn (string $state) => $this->moduleColor($state))
                    ->formatStateUsing(fn (string $state) => ActivityLogger::moduleLabel($state))
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Aktivitas')
                    ->formatStateUsing(fn (string $state) => ActivityLogger::prettifyDescription($state))
                    ->wrap()
                    ->searchable(),
                TextColumn::make('reference')
                    ->label('Referensi')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('module')
                    ->label('Menu')
                    ->options(fn () => ActivityLog::query()
                        ->distinct()
                        ->pluck('module')
                        ->mapWithKeys(fn (string $m) => [$m => ActivityLogger::moduleLabel($m)])
                        ->all()),
                SelectFilter::make('user')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('waktu')
                    ->label('Rentang Waktu')
                    ->columns(2)
->form([
                        DatePicker::make('from')
                            ->label('Dari')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('until')
                            ->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('activity_logs.created_at', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('activity_logs.created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['from'] && !$data['until']) {
                            return null;
                        }
                        return 'Waktu: ' . ($data['from'] ?? '…') . ' → ' . ($data['until'] ?? '…');
                    }),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function moduleColor(string $module): string
    {
        return match ($module) {
            'task_retur_cabangs', 'Retur Cabang' => 'info',
            'supplier_returns', 'Retur Supplier' => 'warning',
            'task_datang_mobil_suppliers', 'Datang Mobil Supplier' => 'info',
            'task_terima_suppliers', 'Terima Supplier' => 'primary',
            'supplier_sjs' => 'info',
            'komplain_pos' => 'danger',
            'branch_shipments' => 'primary',
            'task_keluar_barangs', 'Keluar Barang' => 'warning',
            'task_kiriman_mobils', 'Kiriman Mobil' => 'info',
            'warehouse_documents' => 'success',
            'kendaraan_dokumens' => 'warning',
            'cuti_absensi' => 'success',
            'users', 'roles' => 'primary',
            default => 'gray',
        };
    }

    protected function moduleIcon(string $module): string
    {
        return match ($module) {
            'task_retur_cabangs', 'Retur Cabang' => 'heroicon-m-arrow-uturn-left',
            'supplier_returns', 'Retur Supplier' => 'heroicon-m-arrow-uturn-right',
            'task_datang_mobil_suppliers', 'Datang Mobil Supplier' => 'heroicon-m-home-modern',
            'task_terima_suppliers', 'Terima Supplier' => 'heroicon-m-check-badge',
            'supplier_sjs' => 'heroicon-m-document-text',
            'komplain_pos' => 'heroicon-m-exclamation-triangle',
            'branch_shipments' => 'heroicon-m-paper-airplane',
            'task_keluar_barangs', 'Keluar Barang' => 'heroicon-m-truck',
            'task_kiriman_mobils', 'Kiriman Mobil' => 'heroicon-m-truck',
            'warehouse_documents' => 'heroicon-m-folder',
            'kendaraan_dokumens' => 'heroicon-m-document',
            'cuti_absensi' => 'heroicon-m-calendar-days',
            'master_suppliers' => 'heroicon-m-building-office',
            'master_tokos' => 'heroicon-m-building-storefront',
            'master_kendaraans' => 'heroicon-m-truck',
            'master_sopirs' => 'heroicon-m-identification',
            'expeditions' => 'heroicon-m-globe-alt',
            'warehouse_employees' => 'heroicon-m-users',
            'users' => 'heroicon-m-user-circle',
            'roles' => 'heroicon-m-shield-check',
            default => 'heroicon-m-clock',
        };
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('view_widget_recent_activity') ?? false;
    }
}