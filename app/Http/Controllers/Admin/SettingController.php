<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->authorizeResource(Setting::class, 'setting');
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->index();
    }

    public function update(Request $request, Setting $setting)
    {
        return $this->repository->update($request, $setting?->id);
    }
}
