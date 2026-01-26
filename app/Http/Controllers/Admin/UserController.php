<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;

class UserController extends Controller
{
    private $role;

    private $repository;

    public function __construct(RoleRepository $roleRepository, UserRepository $repository)
    {
        $this->authorizeResource(User::class, 'user');
        $this->repository = $repository;
        $this->role = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(User $user)
    {
        $countries = Country::all()->pluck('name', 'id');
        return view('admin.user.create', ['user' => $user, 'roles' => $this->role->get()], compact('countries'));
    }

    public function store(CreateUserRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(User $user)
    {
        $countries = Country::all()->pluck('name', 'id');
        return view('admin.user.edit', ['user' => $user, 'roles' => $this->role->get()], compact('countries'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        return $this->repository->update($request, $user->id);
    }

    /**
     * Update Status the specified resource from storage.
     *
     * @param  int  $id
     * @param int $status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        return $this->repository->status($id, $request->status);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(User $user)
    {
        return $this->repository->destroy($user->id);
    }

    public function profile()
    {
        $countries = Country::all()->pluck('name', 'id');
        $role = Auth::user()->role->name;
        return view('admin.profile.index', ['user' => Auth::user(), 'role' => Auth::user()->role->name], compact('countries', 'role'));
    }

    public function getStates(Request $request)
    {
        $data['states'] = State::where("country_id", $request->country_id)->get(["name", "id"]);
        return response()->json($data);
    }

    public function editProfile()
    {
        $countries = Country::all()->pluck('name', 'id');
        return view('admin.profile.edit-profile', ['user' => Auth::user()], compact('countries'));
    }

    public function updateProfile(UpdateProfileRequest $request, User $user)
    {
        return $this->repository->updateProfile($request, $user->id);
    }

    public function removeImage($id)
    {
        return $this->repository->removeImage($id);
    }

    public function updatePassword(UpdatePasswordRequest $request, User $user)
    {
        return $this->repository->updatePassword($request, $user->id);
    }

    public function updateEmail(UpdateEmailRequest $request, User $user)
    {
        return $this->repository->updateEmail($request, $user->id);
    }
}
