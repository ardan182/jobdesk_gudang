<?php

namespace App\Filament\Pages;

use App\Models\Division;
use App\Models\WarehouseEmployee;
use App\Models\WarehouseLeave;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageLeaves extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Cuti & Absensi';

    protected static ?string $title = 'Cuti & Absensi';

    protected static ?string $slug = 'cuti-absensi';

    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi';

    public int $bulan = 0;
    public int $tahun = 0;
    public ?int $filter_divisi = null;
    public bool $hanya_absen = false;

    public array $employees = [];
    public array $calendar = [];
    public $divisions = [];

    public function mount(): void
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
        $this->divisions = Division::pluck('nama_divisi', 'id')->toArray();
        $this->loadData();
    }

    public function updated($property): void
    {
        if (in_array($property, ['bulan', 'tahun', 'filter_divisi', 'hanya_absen'])) {
            $this->loadData();
        }
    }

    public function loadData(): void
    {
        $daysInMonth = now()->month($this->bulan)->daysInMonth;
        $this->calendar = range(1, $daysInMonth);
        $startDate = "{$this->tahun}-{$this->bulan}-01";
        $endDate = "{$this->tahun}-{$this->bulan}-{$daysInMonth}";

        $query = WarehouseEmployee::with('division');

        if ($this->filter_divisi) {
            $query->where('division_id', $this->filter_divisi);
        }

        $allEmployees = $query->get();

        $leaves = WarehouseLeave::with('employee')
            ->where('tanggal_mulai', '<=', $endDate)
            ->where('tanggal_selesai', '>=', $startDate)
            ->get();

        $this->employees = [];

        foreach ($allEmployees as $emp) {
            $leaveDays = [];
            foreach ($this->calendar as $day) {
                $date = "{$this->tahun}-" . str_pad($this->bulan, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $leave = $leaves->first(fn ($l) =>
                    $l->warehouse_employee_id === $emp->id &&
                    $l->tanggal_mulai->format('Y-m-d') <= $date &&
                    $l->tanggal_selesai->format('Y-m-d') >= $date
                );
                $leaveDays[$day] = $leave ? $leave->jenis_absen : null;
            }

            $hasAbsen = collect($leaveDays)->filter()->isNotEmpty();

            if ($this->hanya_absen && !$hasAbsen) {
                continue;
            }

            $cutiEntries = WarehouseLeave::where('warehouse_employee_id', $emp->id)
                ->where('jenis_absen', 'Cuti')
                ->whereYear('tanggal_mulai', $this->tahun)
                ->get();

            $cutiDates = collect();
            foreach ($cutiEntries as $entry) {
                $start = $entry->tanggal_mulai->copy();
                $end = $entry->tanggal_selesai->copy();
                while ($start <= $end) {
                    $cutiDates->push($start->format('Y-m-d'));
                    $start->addDay();
                }
            }
            $totalCuti = $cutiDates->unique()->count();

            $this->employees[] = [
                'id' => $emp->id,
                'nama' => $emp->nama_karyawan,
                'leave_days' => $leaveDays,
                'has_absen' => $hasAbsen,
                'sisa_cuti' => max(0, 12 - $totalCuti),
            ];
        }

        $this->employees = collect($this->employees)
            ->sortBy('nama')
            ->values()
            ->toArray();
    }

    public function deleteLeave(int $employeeId, string $date): void
    {
        $leave = WarehouseLeave::where('warehouse_employee_id', $employeeId)
            ->where('tanggal_mulai', '<=', $date)
            ->where('tanggal_selesai', '>=', $date)
            ->first();

        if ($leave) {
            $leave->delete();
            Notification::make()
                ->title('Data absen berhasil dihapus')
                ->success()
                ->send();
            $this->loadData();
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filter')
                    ->columns(5)
                    ->compact()
                    ->schema([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->default($this->bulan)
                            ->live(),
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(array_combine(range(now()->year - 2, now()->year + 2), range(now()->year - 2, now()->year + 2)))
                            ->default($this->tahun)
                            ->live(),
                        Select::make('filter_divisi')
                            ->label('Divisi')
                            ->options(array_merge(['' => 'Semua'], Division::pluck('nama_divisi', 'id')->toArray()))
                            ->default(null)
                            ->live(),
                        Checkbox::make('hanya_absen')
                            ->label('Hanya yang absen')
                            ->live(),
                    ]),
                Html::make(view('filament.pages.manage-leaves-matrix', [
                    'employees' => $this->employees,
                    'calendar' => $this->calendar,
                    'bulan' => $this->bulan,
                    'tahun' => $this->tahun,
                ])->render()),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createLeave')
                ->label('Input Cuti / Absen')
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->modalHeading('Input Cuti / Absen')
                ->modalWidth('lg')
                ->form([
                    Select::make('warehouse_employee_id')
                        ->label('Karyawan')
                        ->options(WarehouseEmployee::with('division')->get()->pluck('nama_karyawan', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('jenis_absen')
                        ->label('Jenis Absen')
                        ->options(['Cuti' => 'Cuti', 'Sakit' => 'Sakit', 'Izin' => 'Izin'])
                        ->required(),
                    DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->default(now()->format('Y-m-d'))
                        ->minDate(now()->format('Y-m-d')),
                    DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai')
                        ->required()
                        ->default(now()->format('Y-m-d'))
                        ->minDate(now()->format('Y-m-d')),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $exists = WarehouseLeave::where('warehouse_employee_id', $data['warehouse_employee_id'])
                        ->where('tanggal_mulai', '<=', $data['tanggal_selesai'])
                        ->where('tanggal_selesai', '>=', $data['tanggal_mulai'])
                        ->exists();

                    if ($exists) {
                        Notification::make()
                            ->title('Duplikat')
                            ->body('Karyawan ini sudah memiliki absen di rentang tanggal tersebut.')
                            ->danger()
                            ->send();
                        return;
                    }

                    if ($data['jenis_absen'] === 'Cuti') {
                        $cutiEntries = WarehouseLeave::where('warehouse_employee_id', $data['warehouse_employee_id'])
                            ->where('jenis_absen', 'Cuti')
                            ->whereYear('tanggal_mulai', now()->year)
                            ->get();

                        $cutiDates = collect();
                        foreach ($cutiEntries as $entry) {
                            $start = $entry->tanggal_mulai->copy();
                            $end = $entry->tanggal_selesai->copy();
                            while ($start <= $end) {
                                $cutiDates->push($start->format('Y-m-d'));
                                $start->addDay();
                            }
                        }

                        $newStart = Carbon::parse($data['tanggal_mulai']);
                        $newEnd = Carbon::parse($data['tanggal_selesai']);
                        while ($newStart <= $newEnd) {
                            $cutiDates->push($newStart->format('Y-m-d'));
                            $newStart->addDay();
                        }

                        if ($cutiDates->unique()->count() > 12) {
                            Notification::make()
                                ->title('Jatah cuti habis')
                                ->body('Cuti tahun ini sudah 12 hari. Tidak bisa input cuti lagi.')
                                ->danger()
                                ->send();
                            return;
                        }
                    }

                    WarehouseLeave::create($data);
                    Notification::make()->title('Data absen berhasil disimpan')->success()->send();
                    $this->loadData();
                }),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }
}
