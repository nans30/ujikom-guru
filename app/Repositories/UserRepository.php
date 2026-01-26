<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;
use Spatie\Permission\Models\Role;

class UserRepository extends BaseRepository
{
    protected $role;

    public function model()
    {
        $this->role = new Role();
        return User::class;
    }

    public function index($userTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('admin.user.index', ['tableConfig' => $userTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $user = $this->model->create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
          
                'phone' => (string) $request->phone,
                'status' => $request->status,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
              
             
             
                'location' => $request->location,
        
           
          
            ]);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $user->addMediaFromRequest('image')->toMediaCollection('image');
            }

            if ($request->role_id) {
                $role = $this->role->findOrFail($request->role_id);
                $user->assignRole($role);
            }

            DB::commit();

            return redirect()->route('admin.user.index')->with('success', 'User Created Successfully');
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {

            $user = $this->model->findOrFail($id);
            $user->update([
               
                'email' => $request->email,
                'phone' => (string) $request->phone,
                'status' => $request->status,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
              
              
            
                'location' => $request->location,
             
              
               
            ]);

            if ($request->password) {
                $user->password = Hash::make($request->password);
                $user->update();
            }

            $user = $this->model->findOrFail($id);
            if ($user->system_reserve) {
                return redirect()->back()->with('error', 'This user cannot be update, It is system reserved.');
            }

            if (isset($request->role_id)) {
                $role = $this->role->find($request->role_id);
                $user->syncRoles($role);
            }

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $user->clearMediaCollection('image');
                $user->addMediaFromRequest('image')->toMediaCollection('image');
            }

            DB::commit();
            return redirect()->route('admin.user.index')->with('success', 'User Updated Successfully');
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function status($id, $status)
    {
        try {

            $user = $this->model->findOrFail($id);
            $user->update(['status' => $status]);

            return json_encode(["resp" => $user]);
        } catch (Exception $e) {

            throw $e;
        }
    }

    public function destroy($id)
    {
        try {

            $user = $this->model->findOrFail($id);
            $user->destroy($id);
            return redirect()->back()->with('success', 'User Deleted Successfully');
        } catch (Exception $e) {

            throw $e;
        }
    }

    public function updateProfile($request, $id)
    {
        DB::beginTransaction();

        try {
            $user = $this->model->findOrFail($id);

            // if ($user->system_reserve) {
            //     return redirect()->back()->with('error', 'This user cannot be updated. It is system reserved.');
            // }

            $user->update([
              
                'phone' => (string) $request->phone,
                // 'status' => $request->status,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            
                
            
                'location' => $request->location,
                
         
              
            ]);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $user->clearMediaCollection('image');
                $user->addMediaFromRequest('image')->toMediaCollection('image');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Profile updated successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateEmail($request, $id)
    {
        DB::beginTransaction();

        try {
            $user = $this->model->findOrFail($id);

            // if ($user->system_reserve) {
            //     return redirect()->back()->with('error', 'This user cannot be updated. It is system reserved.');
            // }

            // Jika kamu punya sistem verifikasi email, bisa ditambahkan di sini
            $user->update([
                'email' => $request->email,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Email updated successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function removeImage($id)
    {
        $user = User::find($id);
        $user->clearMediaCollection('image');
        return redirect()->back()->with('success', 'Image Removed Successfully');
    }

    public function updatePassword($request, $id)
    {
        DB::beginTransaction();

        try {
            $user = $this->model->findOrFail($id);

            // if ($user->system_reserve) {
            //     return redirect()->back()->with('error', 'This user cannot be updated. It is system reserved.');
            // }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Password updated successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}