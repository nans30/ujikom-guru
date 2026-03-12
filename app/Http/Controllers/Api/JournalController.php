<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class JournalController extends Controller
{
    public function index()
    {
        $journals = Journal::with(['teacher', 'schedule'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $journals
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['teacher_id', 'schedule_id', 'date', 'title', 'description', 'status']);
            $data['created_by_id'] = Auth::id() ?? 1;

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment')->store('journals', 'public');
            }

            $journal = Journal::create($data);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Jurnal berhasil disimpan', 'data' => $journal]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $journal = Journal::with(['teacher', 'schedule'])->find($id);
        if (!$journal) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $journal]);
    }

    public function update(Request $request, $id)
    {
        $journal = Journal::find($id);
        if (!$journal) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['teacher_id', 'schedule_id', 'date', 'title', 'description', 'status']);

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment')->store('journals', 'public');
            }

            $journal->update($data);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Jurnal berhasil diperbarui', 'data' => $journal]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $journal = Journal::find($id);
        if (!$journal) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }
        
        $journal->delete();
        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
