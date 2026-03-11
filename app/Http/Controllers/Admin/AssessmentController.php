<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\DataTables\AssessmentDataTable;
use App\Repositories\AssessmentRepository;
use App\Http\Requests\CreateAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    protected $repository;

    public function __construct(AssessmentRepository $repository)
    {
        // Menggunakan Policy untuk keamanan akses (Index, Create, Store, dsb)
        $this->authorizeResource(Assessment::class, 'assessment');
        $this->repository = $repository;
    }

    /**
     * Menampilkan daftar penilaian menggunakan Yajra DataTables
     */
    public function index(AssessmentDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    /**
     * Menampilkan form input penilaian (mengambil kategori & list guru di repo)
     */
    public function create()
    {
        return $this->repository->create();
    }

    /**
     * Menyimpan data penilaian header dan detail (skor bintang)
     */
    public function store(CreateAssessmentRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Menampilkan rincian hasil penilaian (View Only)
     */
    public function show(Assessment $assessment)
    {
        // Kita kirim ID-nya agar Repository bisa melakukan Eager Loading (with details)
        return $this->repository->show($assessment->id);
    }

    /**
     * Menampilkan form edit penilaian
     */
    public function edit(Assessment $assessment)
    {
        return $this->repository->edit($assessment->id);
    }

    /**
     * Memperbarui data penilaian dan detail skor
     */
    public function update(UpdateAssessmentRequest $request, Assessment $assessment)
    {
        return $this->repository->update($request, $assessment->id);
    }

    /**
     * Menghapus data penilaian (Soft Delete)
     */
    public function destroy(Assessment $assessment)
    {
        return $this->repository->destroy($assessment->id);
    }

    /**
     * Mengubah status penilaian (misal: dari Draft ke Final)
     */
    public function status(Request $request, $id)
    {
        return $this->repository->status($id, $request->status);
    }

    /**
     * Menghapus banyak data sekaligus melalui checkbox di DataTable
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'Pilih data yang ingin dihapus terlebih dahulu');
        }

        return $this->repository->bulkDelete($ids);
    }

    /**
     * Fitur opsional untuk menduplikasi data penilaian
     */
    public function copy($id)
    {
        return $this->repository->edit($id, true);
    }
}
