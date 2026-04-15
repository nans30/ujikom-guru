<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointLedger;
use App\Models\Teacher;
use Illuminate\Http\Request;

class PointReportController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->get('teacher_id');
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $ledgers = PointLedger::with('teacher')
            ->when($teacherId, function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->when($type, function ($q) use ($type) {
                $q->where('transaction_type', $type);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $teachers = Teacher::orderBy('name', 'asc')->get();

        return view('admin.reports.points', compact('ledgers', 'teachers'));
    }
}
