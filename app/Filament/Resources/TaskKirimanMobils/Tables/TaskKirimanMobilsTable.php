<?php

namespace App\Filament\Resources\TaskKirimanMobils\Tables;

use App\Filament\Resources\TaskKirimanMobils\Schemas\TaskKirimanMobilForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class TaskKirimanMobilsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('branchShipments'))
            ->columns([
                TextColumn::make('id_task')
                    ->label('ID Task')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->width('120px')
                    ->grow(false),
                TextColumn::make('branch_sj_list')
                    ->label('SJ')
                    ->badge()
                    ->color('info')
                    ->tooltip(fn ($record) => $record->branchShipments->pluck('nomor_sj')->implode(', '))
                    ->getStateUsing(function ($record) {
                        $sj = $record->branchShipments->pluck('nomor_sj');
                        $result = $sj->take(2)->toArray();
                        if ($sj->count() > 2) {
                            $result[] = '+' . ($sj->count() - 2) . ' more';
                        }
                        return $result;
                    })
                    ->grow(false),
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->searchable()
                    ->width('130px')
                    ->grow(false),
                TextColumn::make('jam_muat')
                    ->label('Jam Muat')
                    ->time('H:i')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jam_selesai_muat')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jam_berangkat')
                    ->label('Brkt')
                    ->time('H:i')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jam_tiba')
                    ->label('Tiba')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dalam pengiriman' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'dalam pengiriman' => 'Dalam Pengiriman',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->grow(false),
                TextColumn::make('retur_option')
                    ->label('Retur')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tidak_ada_retur' => 'gray',
                        'ada_rb' => 'warning',
                        'ada_rj' => 'info',
                        'rb_dan_rj' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tidak_ada_retur' => 'Tidak Ada Retur',
                        'ada_rb' => 'Ada RB',
                        'ada_rj' => 'Ada RJ',
                        'rb_dan_rj' => 'Retur RB dan RJ',
                        default => $state,
                    })
                    ->grow(false),
                TextColumn::make('nama_supir')
                    ->label('Supir')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('user.name')
                    ->label('Checker')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false)
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
            ])
            ->filters([
                Filter::make('status')
                    ->label('Status')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'dalam pengiriman' => 'Dalam Pengiriman',
                                'datang' => 'Datang',
                            ])
                            ->placeholder('Semua Status'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['status'] ?? null,
                            fn (Builder $query, $status): Builder => $query->where('status', $status),
                        );
                    }),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Kiriman Mobil')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Task')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('cabang')->label('Cabang'),
                                 TextEntry::make('branch_sj_list')
                                     ->label('SJ')
                                     ->badge()
                                     ->color('info')
                                     ->tooltip(fn ($record) => $record->branchShipments->pluck('nomor_sj')->implode(', '))
                                     ->state(function ($record) {
                                         $sj = $record->branchShipments->pluck('nomor_sj');
                                         $result = $sj->take(2)->toArray();
                                         if ($sj->count() > 2) {
                                             $result[] = '+' . ($sj->count() - 2) . ' more';
                                         }
                                         return $result;
                                     }),
                                TextEntry::make('no_plat_mobil')->label('No Plat'),
                                TextEntry::make('jam_muat')->label('Jam Muat'),
                                TextEntry::make('jam_selesai_muat')->label('Jam Selesai'),
                                TextEntry::make('jam_berangkat')->label('Jam Berangkat'),
                                TextEntry::make('jam_tiba')->label('Jam Tiba'),
                                 TextEntry::make('status')->label('Status')->badge()
                                     ->color(fn (string $state): string => match ($state) {
                                         'draft' => 'gray',
                                         'dalam pengiriman' => 'warning',
                                         'selesai' => 'success',
                                         default => 'gray',
                                     }),
                                TextEntry::make('retur_option')->label('Retur')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'tidak_ada_retur' => 'gray',
                                        'ada_rb' => 'warning',
                                        'ada_rj' => 'info',
                                        'rb_dan_rj' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'tidak_ada_retur' => 'Tidak Ada Retur',
                                        'ada_rb' => 'Ada RB',
                                        'ada_rj' => 'Ada RJ',
                                        'rb_dan_rj' => 'Retur RB dan RJ',
                                        default => $state,
                                    }),
                                 TextEntry::make('nama_supir')->label('Supir'),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::Full)
                    ->form(TaskKirimanMobilForm::getFormFields())
                    ->using(function ($record, array $data) {
                        $sjs = $data['branch_shipments'] ?? [];
                        unset(
                            $data['branch_shipments'],
                            $data['total_sj_tampil'],
                            $data['sisa_sj_tampil'],
                            $data['durasi_tampil'],
                        );
                        $record->update($data);
                        $record->branchShipments()->sync(filled($sjs) ? $sjs : []);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }
}
