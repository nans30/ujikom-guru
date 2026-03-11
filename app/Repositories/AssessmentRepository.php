<?php

namespace App\Repositories;

use Exception;
use App\Models\Categorie;
use App\Models\Teacher;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class AssessmentRepository extends BaseRepository
{
    /**
     * Tentukan model yang digunakan oleh repository ini
     */
    public function model()
    {
        return Assessment::class;
    }

    /**
     * Menampilkan view index melalui DataTable
     */
    public function index($dataTable)
    {
        return $dataTable->render('admin.assessment.index');
    }

    /**
     * Menyiapkan data untuk form Create
     */
    public function create(array $attributes = [])
    {
        // Ambil kategori penilaian yang aktif
        $categories = Categorie::where('status', true)->get();

        // Ambil daftar Guru yang aktif
        $evaluatees = Teacher::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // Menangkap teacher_id dari parameter URL (?teacher_id=...)
        // Ini digunakan untuk auto-select guru saat klik tombol "+" di monitoring
        $selectedTeacherId = request('teacher_id');

        return view('admin.assessment.create', compact('categories', 'evaluatees', 'selectedTeacherId'));
    }

    /**
     * Menyimpan data Penilaian (Header & Detail Skor)
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            // 1. Tangkap Data Header sesuai migrasi baru
            $data = $request->only([
                'evaluatee_id',
                'assessment_date',
                'semester',
                'academic_year',
                'general_notes',
                'status',
            ]);

            // Evaluator (Penilai) adalah Admin yang sedang login
            $data['evaluator_id'] = Auth::id();

            // 2. Simpan ke tabel assessments (Header)
            $assessment = $this->model->create($data);

            // 3. Simpan Detail Nilai (Star Rating)
            if ($request->has('scores')) {
                $details = [];
                foreach ($request->input('scores') as $categoryId => $score) {
                    $details[] = [
                        'category_id' => $categoryId,
                        'score'       => $score,
                    ];
                }

                // Simpan massal melalui relasi details()
                $assessment->details()->createMany($details);
            }

            DB::commit();
            return redirect()->route('admin.assessment.index')->with('success', 'Penilaian berhasil disimpan');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Menampilkan rincian penilaian (Eager Loading)
     */
    public function show($id)
    {
        $model = $this->model->with(['details.category', 'evaluator', 'evaluatee'])->findOrFail($id);

        return view('admin.assessment.show', [
            'assessment' => $model,
        ]);
    }

    /**
     * Menyiapkan data untuk form Edit
     */
    public function edit($id)
    {
        $model = $this->model->with('details')->findOrFail($id);
        $categories = Categorie::where('status', true)->get();
        $evaluatees = Teacher::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.assessment.edit', [
            'assessment' => $model,
            'categories' => $categories,
            'evaluatees' => $evaluatees,
        ]);
    }

    /**
     * Memperbarui data Penilaian dan Detail Skor
     */
    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $assessment = $this->model->findOrFail($id);

            // Update Data Header
            $data = $request->only([
                'evaluatee_id',
                'assessment_date',
                'semester',
                'academic_year',
                'general_notes',
                'status',
            ]);

            $assessment->update($data);

            // Update Detail Nilai (Metode: Delete & Re-insert)
            if ($request->has('scores')) {
                $assessment->details()->delete();

                $details = [];
                foreach ($request->input('scores') as $categoryId => $score) {
                    $details[] = [
                        'category_id' => $categoryId,
                        'score'       => $score,
                    ];
                }
                $assessment->details()->createMany($details);
            }

            DB::commit();
            return redirect()->route('admin.assessment.index')->with('success', 'Penilaian berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Menghapus data Penilaian (Soft Delete)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $model = $this->model->findOrFail($id);
            $model->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}
