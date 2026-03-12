<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with(['details.category', 'evaluator', 'evaluatee'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $assessments
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'evaluatee_id' => 'required|exists:teachers,id',
            'assessment_date' => 'required|date',
            'semester' => 'required',
            'academic_year' => 'required',
            'scores' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['evaluatee_id', 'assessment_date', 'semester', 'academic_year', 'general_notes', 'status']);
            $data['evaluator_id'] = Auth::id() ?? 1;

            $assessment = Assessment::create($data);

            if ($request->has('scores')) {
                $details = [];
                foreach ($request->input('scores') as $categoryId => $score) {
                    $details[] = [
                        'category_id' => $categoryId,
                        'score' => $score,
                    ];
                }
                $assessment->details()->createMany($details);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Penilaian berhasil disimpan', 'data' => $assessment->load('details')]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $assessment = Assessment::with(['details.category', 'evaluator', 'evaluatee'])->find($id);
        if (!$assessment) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $assessment]);
    }

    public function update(Request $request, $id)
    {
        $assessment = Assessment::find($id);
        if (!$assessment) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['evaluatee_id', 'assessment_date', 'semester', 'academic_year', 'general_notes', 'status']);
            $assessment->update($data);

            if ($request->has('scores')) {
                $assessment->details()->delete();
                $details = [];
                foreach ($request->input('scores') as $categoryId => $score) {
                    $details[] = [
                        'category_id' => $categoryId,
                        'score' => $score,
                    ];
                }
                $assessment->details()->createMany($details);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Penilaian berhasil diperbarui', 'data' => $assessment->load('details')]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $assessment = Assessment::find($id);
        if (!$assessment) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }
        
        $assessment->delete();
        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
