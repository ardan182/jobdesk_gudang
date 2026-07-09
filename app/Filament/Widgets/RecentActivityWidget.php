<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class RecentActivityWidget extends Widget
{
    protected string $view = 'filament.widgets.recent-activity';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 100;

    public int $perPage = 10;

    public int $currentPage = 1;

    public function gotoPage(int $page): void
    {
        $this->currentPage = max(1, $page);
    }

    public function updatedPerPage(): void
    {
        $this->currentPage = 1;
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['Admin', 'Checker Retur', 'Checker Terima', 'Checker Keluar', 'Checker Kiriman']);
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        $isAdmin = $user?->hasRole('Admin') ?? false;
        $userId = $user?->id;

        $modules = [
            'task_retur_suppliers' => 'Retur Supplier',
            'task_retur_cabangs' => 'Retur Cabang',
            'task_terima_suppliers' => 'Terima Supplier',
            'task_keluar_barangs' => 'Keluar Barang',
            'task_kiriman_mobils' => 'Kiriman Mobil',
        ];

        $first = true;
        $query = null;

        foreach ($modules as $table => $label) {
            $q = DB::table($table)
                ->selectRaw("'{$label}' as module, id_task, user_id, created_at");

            if (! $isAdmin) {
                $q->where('user_id', $userId);
            }

            $query = $first ? $q : $query->unionAll($q);
            $first = false;
        }

        $total = $query->count();
        $rows = $query->orderByDesc('created_at')
            ->skip(($this->currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        $userIds = $rows->pluck('user_id')->unique()->filter()->values()->toArray();
        $users = User::whereIn('id', $userIds)->pluck('name', 'id');

        $activities = [];
        foreach ($rows as $row) {
            $activities[] = [
                'user' => $users[$row->user_id] ?? '-',
                'activity' => $row->module . ' — ' . $row->id_task,
                'time' => $row->created_at,
            ];
        }

        return [
            'activities' => $activities,
            'total' => $total,
            'perPage' => $this->perPage,
            'currentPage' => $this->currentPage,
            'lastPage' => max(1, (int) ceil($total / $this->perPage)),
        ];
    }
}
