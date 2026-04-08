<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockHistory;
use App\Models\Lot;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard view.
     */
    public function index()
    {
        return view('dashboard.index');
    }

    /**
     * Inventory pie chart data.
     */
    public function inventoryChart()
    {
        $counts = Lot::select('estado', DB::raw('COUNT(*) as total'))
            ->where('estado', '!=', Lot::ESTADO_OCULTO)
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $data = [];
        foreach (Lot::ESTADO_LABELS as $key => $label) {
            if ($key === Lot::ESTADO_OCULTO) continue;
            $data[] = [
                'label' => $label,
                'value' => $counts[$key] ?? 0,
                'color' => Lot::ESTADO_COLORS[$key],
            ];
        }

        $total = Lot::where('estado', '!=', Lot::ESTADO_OCULTO)->count();

        return response()->json([
            'data' => $data,
            'total' => $total,
        ]);
    }

    /**
     * Trend line chart data: accumulated blocks and reservations from day 1 to today.
     */
    public function trendChart(Request $request)
    {
        $startDate = SystemSetting::get('trend_start_date', Carbon::today()->subMonth()->toDateString());
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::today()->endOfDay();

        // Blocks created per day
        $blocks = BlockHistory::where('action', BlockHistory::ACTION_CREATED)
            ->where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Reservations per day
        $reservations = BlockHistory::where('action', BlockHistory::ACTION_RESERVED)
            ->where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Build cumulative data
        $labels = [];
        $blocksCumulative = [];
        $reservationsCumulative = [];
        $blockTotal = 0;
        $reservationTotal = 0;

        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->toDateString();
            $labels[] = $current->format('d/m');
            $blockTotal += $blocks[$dateStr] ?? 0;
            $reservationTotal += $reservations[$dateStr] ?? 0;
            $blocksCumulative[] = $blockTotal;
            $reservationsCumulative[] = $reservationTotal;
            $current->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'blocks' => $blocksCumulative,
            'reservations' => $reservationsCumulative,
        ]);
    }

    /**
     * Performance bar chart: blocks per seller.
     */
    public function performanceChart(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $query = BlockHistory::where('action', BlockHistory::ACTION_CREATED)
            ->join('users', 'block_history.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(*) as total'));

        if ($request->filled('from')) {
            $query->where('block_history.created_at', '>=', Carbon::parse($request->from)->startOfDay());
        }
        if ($request->filled('to')) {
            $query->where('block_history.created_at', '<=', Carbon::parse($request->to)->endOfDay());
        }

        $data = $query->groupBy('users.name')->orderByDesc('total')->get();

        return response()->json([
            'labels' => $data->pluck('name'),
            'values' => $data->pluck('total'),
        ]);
    }

    /**
     * KPI data: blocks/day, expired/day, reservations/day.
     */
    public function kpis()
    {
        $today = Carbon::today();

        $blocksToday = BlockHistory::where('action', BlockHistory::ACTION_CREATED)
            ->whereDate('created_at', $today)
            ->count();

        $expiredToday = BlockHistory::where('action', BlockHistory::ACTION_EXPIRED)
            ->whereDate('created_at', $today)
            ->count();

        $reservationsToday = BlockHistory::where('action', BlockHistory::ACTION_RESERVED)
            ->whereDate('created_at', $today)
            ->count();

        return response()->json([
            'blocks_today' => $blocksToday,
            'expired_today' => $expiredToday,
            'reservations_today' => $reservationsToday,
        ]);
    }

    /**
     * Expired blocks report with detail.
     */
    public function expiredReport(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $query = BlockHistory::where('action', BlockHistory::ACTION_EXPIRED)
            ->with(['lot', 'user'])
            ->orderByDesc('created_at');

        if ($request->filled('from')) {
            $query->where('created_at', '>=', Carbon::parse($request->from)->startOfDay());
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', Carbon::parse($request->to)->endOfDay());
        }

        $report = $query->paginate(25);

        $items = $report->map(function ($entry) {
            return [
                'date' => $entry->created_at->format('d/m/Y H:i'),
                'manzana' => $entry->lot->manzana ?? '-',
                'nro_lote' => $entry->lot->nro_lote ?? '-',
                'vendedor' => $entry->user->name ?? '-',
                'client_name' => $entry->client_name,
                'client_phone' => $entry->client_phone,
            ];
        });

        return response()->json([
            'data' => $items,
            'pagination' => [
                'current_page' => $report->currentPage(),
                'last_page' => $report->lastPage(),
                'total' => $report->total(),
            ],
        ]);
    }
}
