<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::query()
            ->when($request->event, fn($q) => $q->where('event', $request->event))
            ->when($request->model, fn($q) => $q->where('model_type', 'LIKE', "%{$request->model}%"))
            ->latest()
            ->paginate(20);

        return response()->json($logs);
    }
}
