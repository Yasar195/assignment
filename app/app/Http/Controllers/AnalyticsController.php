<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function ingest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_name' => ['required', 'string', 'max:255'],
            'page' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:50'],
            'session_id' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ]);

        $event = Event::create([
            'event_name' => $validated['event_name'],
            'page' => $validated['page'] ?? null,
            'source' => $validated['source'] ?? 'browser',
            'session_id' => $validated['session_id'] ?? $request->header('X-Session-Id'),
            'user_id' => $validated['user_id'] ?? $request->header('X-User-Id'),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'metadata' => $validated['metadata'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'event' => $event,
        ], 201);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $start = $request->query('start', Carbon::now()->subDays(7)->toDateString());
        $end = $request->query('end', Carbon::now()->toDateString());

        $events = Event::whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59'])
            ->get();

        $pageViews = $events->where('event_name', 'page_view')->count();
        $uniqueVisitors = $events->pluck('session_id')->filter()->unique()->count();
        $popularPages = $events
            ->where('event_name', 'page_view')
            ->groupBy('page')
            ->map(fn ($group) => [
                'page' => $group->first()->page,
                'count' => $group->count(),
            ])
            ->values()
            ->sortByDesc('count')
            ->take(5)
            ->values();

        $eventTrend = Event::whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, event_name, COUNT(*) as count')
            ->groupByRaw('DATE(created_at), event_name')
            ->get();

        return response()->json([
            'summary' => [
                'page_views' => $pageViews,
                'unique_visitors' => $uniqueVisitors,
                'total_events' => $events->count(),
            ],
            'popular_pages' => $popularPages,
            'event_trend' => $eventTrend,
        ]);
    }
}
