<?php

namespace App\Repositories;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Teacher;

class TeacherRepository extends BaseRepository
{
    public function model()
    {
        return Teacher::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.teacher.index');
    }

    public function create(array $attributes = [])
    {
        return view('admin.teacher.create', $attributes);
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $data = $request->only([
                'nip',
                'name',
                'email',
                'rfid_uid',
                'is_active',
            ]);

            $data['created_by_id'] = Auth::id();

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            /** @var Teacher $teacher */
            $teacher = $this->model->create($data);

            // upload photo (Spatie)
            if ($request->hasFile('photo')) {
                $teacher
                    ->addMediaFromRequest('photo')
                    ->toMediaCollection('photo');
            }

            DB::commit();

            return redirect()
                ->route('admin.teacher.index')
                ->with('success', 'Teacher created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function edit($id)
    {
        $teacher = $this->model->findOrFail($id);

        return view('admin.teacher.edit', compact('teacher'));
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            /** @var Teacher $teacher */
            $teacher = $this->model->findOrFail($id);

            $data = $request->only([
                'nip',
                'name',
                'email',
                'rfid_uid',
                'is_active',
            ]);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $teacher->update($data);

            // upload photo (auto replace karena singleFile)
            if ($request->hasFile('photo')) {
                $teacher
                    ->addMediaFromRequest('photo')
                    ->toMediaCollection('photo');
            }

            DB::commit();

            return redirect()
                ->route('admin.teacher.index')
                ->with('success', 'Teacher updated successfully');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            /** @var Teacher $teacher */
            $teacher = $this->model->findOrFail($id);

            // hapus media (optional, tapi rapi)
            $teacher->clearMediaCollection('photo');

            $teacher->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Teacher deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return back()->with('error', 'No items selected');
            }

            $teachers = $this->model->whereIn('id', $ids)->get();

            foreach ($teachers as $teacher) {
                $teacher->clearMediaCollection('photo');
                $teacher->delete();
            }

            DB::commit();

            return back()->with('success', 'Selected teachers deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $teacher = $this->model->findOrFail($id);

            $teacher->update([
                'is_active' => ! $teacher->is_active
            ]);

            return response()->json([
                'success' => true,
                'status' => $teacher->is_active
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}