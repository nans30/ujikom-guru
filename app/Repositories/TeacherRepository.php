<?php

namespace App\Repositories;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Teacher;
use App\Models\User;

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

    /*
    |------------------------------------------------------------------
    | STORE
    |------------------------------------------------------------------
    */
    public function store($request)
    {
        DB::beginTransaction();

        try {
            /**
             * ---------------------------------------------
             * CREATE USER (OPTIONAL - LOGIN)
             * ---------------------------------------------
             */
            $user = null;

            if ($request->filled('email')) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make(
                        $request->password ?? 'password123'
                    ),
                    'status'   => 1,
                ]);

                // spatie role
                $user->assignRole('user'); // atau 'teacher'
            }

            /**
             * ---------------------------------------------
             * CREATE TEACHER
             * ---------------------------------------------
             */
            $data = $request->only([
                'nip',
                'name',
                'nuptk',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'nik',
                'rfid_uid',
                'is_active',
            ]);

            $data['created_by_id'] = Auth::id();
            $data['user_id']       = $user?->id;

            /** @var Teacher $teacher */
            $teacher = $this->model->create($data);

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

    /*
    |------------------------------------------------------------------
    | EDIT
    |------------------------------------------------------------------
    */
    public function edit($id)
    {
        $teacher = $this->model->with('user')->findOrFail($id);
        return view('admin.teacher.edit', compact('teacher'));
    }

    /*
    |------------------------------------------------------------------
    | UPDATE
    |------------------------------------------------------------------
    */
    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            /** @var Teacher $teacher */
            $teacher = $this->model->with('user')->findOrFail($id);

            /**
             * ---------------------------------------------
             * HANDLE EMAIL → USER
             * ---------------------------------------------
             */
            if ($request->filled('email')) {

                // BELUM ADA USER → BUAT
                if (! $teacher->user) {
                    $user = User::create([
                        'name'     => $request->name,
                        'email'    => $request->email,
                        'password' => Hash::make(
                            $request->password ?? 'password123'
                        ),
                        'status'   => 1,
                    ]);

                    $user->assignRole('user');

                    $teacher->update([
                        'user_id' => $user->id
                    ]);
                }
                // SUDAH ADA USER → UPDATE
                else {
                    $teacher->user->update([
                        'name'  => $request->name,
                        'email' => $request->email,
                    ]);

                    if ($request->filled('password')) {
                        $teacher->user->update([
                            'password' => Hash::make($request->password),
                        ]);
                    }
                }
            }

            /**
             * ---------------------------------------------
             * UPDATE TEACHER (DATA PEGAWAIAN)
             * ---------------------------------------------
             */
            $data = $request->only([
                'nip',
                'name',
                'nuptk',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'nik',
                'rfid_uid',
                'is_active',
            ]);

            $teacher->update($data);

            if ($request->hasFile('photo')) {
                $teacher->clearMediaCollection('photo');
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

    /*
    |------------------------------------------------------------------
    | DELETE
    |------------------------------------------------------------------
    */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $teacher = $this->model->with('user')->findOrFail($id);

            if ($teacher->user) {
                $teacher->user->delete();
            }

            $teacher->clearMediaCollection('photo');
            $teacher->delete();

            DB::commit();

            return back()->with('success', 'Teacher deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |------------------------------------------------------------------
    | BULK DELETE
    |------------------------------------------------------------------
    */
    public function bulkDestroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return back()->with('error', 'No items selected');
            }

            $teachers = $this->model->with('user')
                ->whereIn('id', $ids)
                ->get();

            foreach ($teachers as $teacher) {
                if ($teacher->user) {
                    $teacher->user->delete();
                }

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

    /*
    |------------------------------------------------------------------
    | TOGGLE STATUS
    |------------------------------------------------------------------
    */
    public function toggleStatus($id)
    {
        $teacher = $this->model->findOrFail($id);

        $teacher->update([
            'is_active' => ! $teacher->is_active
        ]);

        return response()->json([
            'success' => true,
            'status'  => $teacher->is_active
        ]);
    }
}