<?php

namespace App\Filament\Pages;

use App\Models\TvBoardSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageTvBoard extends Page
{
    protected string $view = 'filament.pages.manage-tv-board';

    protected static ?string $title = 'Pengaturan Board TV';
    protected static ?string $slug = 'settings/tv-board';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-tv';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan';
    }

    public int $refresh_interval = 60;
    public int $max_items = 15;
    public bool $show_supplier_arrivals = true;
    public bool $show_branch_deliveries = true;
    public bool $show_shipment_sj = true;
    public bool $show_supplier_invoices = true;
    public ?string $marquee_message = null;

    public function mount(): void
    {
        $settings = TvBoardSetting::first();
        if ($settings) {
            $this->refresh_interval = $settings->refresh_interval;
            $this->max_items = $settings->max_items;
            $this->show_supplier_arrivals = $settings->show_supplier_arrivals;
            $this->show_branch_deliveries = $settings->show_branch_deliveries;
            $this->show_shipment_sj = $settings->show_shipment_sj;
            $this->show_supplier_invoices = $settings->show_supplier_invoices;
            $this->marquee_message = $settings->marquee_message;
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Link Board TV')
                    ->schema([
                        TextInput::make('tv_board_url')
                            ->label('URL Board TV')
                            ->default(url('/tv-board'))
                            ->disabled()
                            ->copyable()
                            ->prefixIcon('heroicon-m-link'),
                        Actions::make([
                            Action::make('open')
                                ->label('Buka Board TV di Tab Baru')
                                ->icon('heroicon-m-arrow-top-right-on-square')
                                ->color('info')
                                ->url(url('/tv-board'), shouldOpenInNewTab: true),
                        ]),
                    ]),
                Section::make('Tampilan & Refresh')
                    ->columns(2)
                    ->schema([
                        Select::make('refresh_interval')
                            ->label('Interval Refresh (detik)')
                            ->options([
                                10 => '10 detik',
                                30 => '30 detik',
                                60 => '1 menit',
                                120 => '2 menit',
                                300 => '5 menit',
                            ])
                            ->required(),
                        Select::make('max_items')
                            ->label('Max Baris per Card')
                            ->options([
                                5 => '5', 10 => '10', 15 => '15',
                                20 => '20', 25 => '25', 30 => '30',
                            ])
                            ->required(),
                    ]),
                Section::make('Card yang Ditampilkan')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_supplier_arrivals')
                            ->label('Mobil Supplier Datang'),
                        Toggle::make('show_branch_deliveries')
                            ->label('Mobil Kirim ke Cabang'),
                        Toggle::make('show_shipment_sj')
                            ->label('SJ Kiriman'),
                        Toggle::make('show_supplier_invoices')
                            ->label('Input SJ Supplier'),
                    ]),
                Section::make('Pesan Marquee (opsional)')
                    ->schema([
                        TextInput::make('marquee_message')
                            ->label('Teks Marquee')
                            ->placeholder('Kosongi untuk menggunakan teks otomatis')
                            ->maxLength(255),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->action('saveSettings'),
        ];
    }

    public function saveSettings(): void
    {
        $settings = TvBoardSetting::first() ?? new TvBoardSetting;
        $settings->refresh_interval = $this->refresh_interval;
        $settings->max_items = $this->max_items;
        $settings->show_supplier_arrivals = $this->show_supplier_arrivals;
        $settings->show_branch_deliveries = $this->show_branch_deliveries;
        $settings->show_shipment_sj = $this->show_shipment_sj;
        $settings->show_supplier_invoices = $this->show_supplier_invoices;
        $settings->marquee_message = $this->marquee_message;
        $settings->save();

        Notification::make()
            ->title('Pengaturan Board TV tersimpan')
            ->success()
            ->send();
    }
}
