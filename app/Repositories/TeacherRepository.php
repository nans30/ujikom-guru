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
use App\Models\Position;

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
        $positions = Position::all(); // ambil semua posisi
        return view('admin.teacher.create', array_merge($attributes, compact('positions')));
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            // ================= CREATE USER =================
            $user = null;
            if ($request->filled('email')) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password ?? 'password123'),
                    'status'   => 1,
                ]);

                $user->assignRole('user'); // atau 'teacher'
            }

            // ================= CREATE TEACHER =================
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
                'position_id', // <-- posisi
            ]);

            $data['created_by_id'] = Auth::id();
            $data['user_id']       = $user?->id;

            $teacher = $this->model->create($data);

            if ($request->hasFile('photo')) {
                $teacher->addMediaFromRequest('photo')->toMediaCollection('photo');
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
        $teacher = $this->model->with('user')->findOrFail($id);
        $positions = Position::all(); // ambil semua posisi
        return view('admin.teacher.edit', compact('teacher', 'positions'));
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $teacher = $this->model->with('user')->findOrFail($id);

            // ================= HANDLE EMAIL =================
            if ($request->filled('email')) {
                if (! $teacher->user) {
                    $user = User::create([
                        'name'     => $request->name,
                        'email'    => $request->email,
                        'password' => Hash::make($request->password ?? 'password123'),
                        'status'   => 1,
                    ]);
                    $user->assignRole('user');

                    $teacher->update(['user_id' => $user->id]);
                } else {
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

            // ================= UPDATE TEACHER =================
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
                'position_id', // <-- posisi
                'point_balance',
            ]);

            $oldPoints = $teacher->point_balance;
            $teacher->update($data);

            // ================= POINT LEDGER LOGGING =================
            if ($request->has('point_balance') && (int)$request->point_balance !== $oldPoints) {
                $diff = (int)$request->point_balance - $oldPoints;
                \App\Models\PointLedger::create([
                    'teacher_id'       => $teacher->id,
                    'transaction_type' => $diff > 0 ? 'EARN' : 'PENALTY',
                    'amount'           => $diff,
                    'current_balance'  => $teacher->point_balance,
                    'description'      => 'Penyesuaian Manual Admin via Edit Profil',
                ]);
            }

            if ($request->hasFile('photo')) {
                $teacher->clearMediaCollection('photo');
                $teacher->addMediaFromRequest('photo')->toMediaCollection('photo');
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

    public function bulkDestroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) return back()->with('error', 'No items selected');

            $teachers = $this->model->with('user')->whereIn('id', $ids)->get();

            foreach ($teachers as $teacher) {
                if ($teacher->user) $teacher->user->delete();
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
        $teacher = $this->model->findOrFail($id);
        $teacher->update(['is_active' => ! $teacher->is_active]);

        return response()->json([
            'success' => true,
            'status'  => $teacher->is_active
        ]);
    }

}