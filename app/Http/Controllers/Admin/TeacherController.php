<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\DataTables\TeacherDataTable;
use App\Repositories\TeacherRepository;
use App\Http\Requests\CreateTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected $repository;

    public function __construct(TeacherRepository $repository)
    {
        $this->authorizeResource(Teacher::class, 'teacher');
        $this->repository = $repository;
    }

    public function index(TeacherDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreateTeacherRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Teacher $teacher)
    {
        return $this->repository->show($teacher);
    }

    public function edit(Teacher $teacher)
    {
        return $this->repository->edit($teacher->id);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        return $this->repository->update($request, $teacher->id);
    }

    public function destroy(Teacher $teacher)
    {
        return $this->repository->destroy($teacher->id);
    }

    public function status(Request $request, $id)
    {
        return $this->repository->status($id, $request->status);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'No IDs selected');
        }

        return $this->repository->bulkDelete($ids);
    }

    public function copy($id)
    {
        return $this->repository->edit($id, true);
    }
}
