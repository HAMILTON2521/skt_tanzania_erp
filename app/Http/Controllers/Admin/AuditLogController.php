<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::query()->with(['causer:id,name', 'subject']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search): void {
                $builder->where('description', 'like', "%{$search}%")
                    ->orWhere('log_name', 'like', "%{$search}%")
                    ->orWhereHas('causer', fn ($causerQuery) => $causerQuery->where('name', 'like', "%{$search}%"));
            });
        }

        return view('admin.audit-logs.index', [
            'navigation' => config('admin.navigation', []),
            'activities' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => [
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }
}
