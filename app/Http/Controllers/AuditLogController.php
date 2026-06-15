<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Show audit logs dashboard
     */
    public function index(Request $request)
    {
        $this->authorize('viewAuditLogs');

        $query = AuditLog::with('user')->latest('created_at');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $auditLogs = $query->paginate(50);
        $users = User::all();

        // Get action statistics
        $statistics = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::where('created_at', '>=', now()->subWeek())->count(),
            'this_month' => AuditLog::where('created_at', '>=', now()->subMonth())->count(),
        ];

        // Get top actions
        $topActions = AuditLog::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Get most active users
        $topUsers = AuditLog::selectRaw('user_id, user_name, COUNT(*) as count')
            ->groupBy('user_id', 'user_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('audit.index', compact(
            'auditLogs',
            'users',
            'statistics',
            'topActions',
            'topUsers'
        ));
    }

    /**
     * Show single audit log
     */
    public function show(AuditLog $auditLog)
    {
        $this->authorize('viewAuditLogs');

        return view('audit.show', compact('auditLog'));
    }

    /**
     * Show user audit history
     */
    public function userHistory(User $user, Request $request)
    {
        $this->authorize('viewAuditLogs');

        $query = AuditLog::where('user_id', $user->id)
            ->latest('created_at');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $auditLogs = $query->paginate(50);

        return view('audit.user-history', compact('user', 'auditLogs'));
    }

    /**
     * Show model history
     */
    public function modelHistory(Request $request)
    {
        $this->authorize('viewAuditLogs');

        $auditableType = $request->query('type');
        $auditableId = $request->query('id');

        if (!$auditableType || !$auditableId) {
            abort(400, 'Type and ID are required');
        }

        $query = AuditLog::where('auditable_type', $auditableType)
            ->where('auditable_id', $auditableId)
            ->latest('created_at');

        $auditLogs = $query->paginate(50);

        return view('audit.model-history', compact('auditLogs', 'auditableType', 'auditableId'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $this->authorize('viewAuditLogs');

        // Log the export action
        AuditService::logExport('audit logs', 0, [
            'filters' => [
                'action' => $request->action,
                'user_id' => $request->user_id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
            ]
        ]);

        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $auditLogs = $query->latest('created_at')->get();

        // Generate CSV
        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($auditLogs) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // Headers
            fputcsv($file, [
                'ID',
                'Utilisateur',
                'Action',
                'Type d\'entité',
                'ID d\'entité',
                'Description',
                'Statut',
                'Adresse IP',
                'Date',
            ]);

            // Data
            foreach ($auditLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user_name,
                    $log->action,
                    class_basename($log->auditable_type ?? ''),
                    $log->auditable_id,
                    $log->description,
                    $log->status,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear old logs (admin only)
     */
    public function clearOldLogs(Request $request)
    {
        $this->authorize('admin');

        $days = $request->input('days', 365);
        $count = AuditLog::where('created_at', '<', now()->subDays($days))->count();

        AuditLog::where('created_at', '<', now()->subDays($days))->delete();

        AuditService::log(
            'delete',
            description: "Suppression de {$count} logs d'audit antérieurs à {$days} jours"
        );

        return redirect()->route('audit.index')
            ->with('success', "Suppression de {$count} logs d'audit.");
    }
}
