<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\DataTables\AttendanceDataTable;
use App\Repositories\AttendanceRepository;
use App\Http\Requests\CreateAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Http\Request;

/**
 * @class AttendanceController
 * @brief Controller untuk mengelola data kehadiran (Attendance).
 * * Controller ini menangani proses CRUD absensi dengan menggunakan 
 * pola Repository dan DataTables untuk penyajian data.
 */
class AttendanceController extends Controller
{
    /**
     * @var AttendanceRepository $repository
     */
    protected $repository;

    /**
     * Membangun instance controller baru.
     * * @param AttendanceRepository $repository
     */
    public function __construct(AttendanceRepository $repository)
    {
        $this->authorizeResource(Attendance::class, 'attendance');
        $this->repository = $repository;
    }

    /**
     * Menampilkan daftar kehadiran menggunakan DataTables.
     * * @param AttendanceDataTable $dataTable
     * @return mixed
     */
    public function index(AttendanceDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    /**
     * Menampilkan form untuk membuat data kehadiran baru.
     * * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->repository->create();
    }

    /**
     * Menyimpan data kehadiran baru ke dalam database.
     * * @param CreateAttendanceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateAttendanceRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Menampilkan detail data kehadiran tertentu.
     * * @param Attendance $attendance
     * @return mixed
     */
    public function show(Attendance $attendance)
    {
        return $this->repository->show($attendance);
    }

    /**
     * Menampilkan form edit untuk data kehadiran.
     * * @param Attendance $attendance
     * @return mixed
     */
    public function edit(Attendance $attendance)
    {
        return $this->repository->edit($attendance->id);
    }

    /**
     * Memperbarui data kehadiran di database.
     * * @param UpdateAttendanceRequest $request
     * @param Attendance $attendance
     * @return mixed
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        return $this->repository->update($request, $attendance->id);
    }

    /**
     * Menghapus data kehadiran dari database.
     * * @param Attendance $attendance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Attendance $attendance)
    {
        return $this->repository->destroy($attendance->id);
    }

    /**
     * Memperbarui status kehadiran secara cepat.
     * * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function status(Request $request, $id)
    {
        return $this->repository->status($id, $request->status);
    }

    /**
     * Menghapus banyak data kehadiran sekaligus (Bulk Delete).
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'No IDs selected');
        }

        return $this->repository->bulkDelete($ids);
    }

    /**
     * Menyalin data kehadiran yang ada untuk mempercepat input.
     * * @param int $id
     * @return mixed
     */
    public function copy($id)
    {
        return $this->repository->edit($id, true);
    }
}
