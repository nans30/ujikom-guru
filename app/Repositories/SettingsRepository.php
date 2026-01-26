<?php

namespace App\Repositories;

use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;


class SettingsRepository extends BaseRepository
{
    public function model()
    {
        return Setting::class;
    }

    public function index()
    {
        $settings = $this->model->pluck('values')->first();
        $settingsId = $this->model->pluck('id')->first();

        return view('admin.settings.index', [
            'settings' => $settings,
            'settingsId' => $settingsId,
        ]);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $settings = $this->model->findOrFail($id);
            $requestData = $request->except(['_token', '_method']);

            // Logo
            if ($request->hasFile('general.logo')) {
                $media = $settings->addMediaFromRequest('general.logo')->toMediaCollection('logo');
                $requestData['general']['logo'] = $media->getUrl();
            } else {
                $requestData['general']['logo'] = $settings->values['general']['logo'] ?? null;
            }

            // Logo Small
            if ($request->hasFile('general.logo_sm')) {
                $media = $settings->addMediaFromRequest('general.logo_sm')->toMediaCollection('logo_sm');
                $requestData['general']['logo_sm'] = $media->getUrl();
            } else {
                $requestData['general']['logo_sm'] = $settings->values['general']['logo_sm'] ?? null;
            }

            // Favicon
            if ($request->hasFile('general.favicon')) {
                $media = $settings->addMediaFromRequest('general.favicon')->toMediaCollection('favicon');
                $requestData['general']['favicon'] = $media->getUrl();
            } else {
                $requestData['general']['favicon'] = $settings->values['general']['favicon'] ?? null;
            }

            // Optional: update .env jika diperlukan
            $this->env($requestData);

            $settings->update([
                'values' => $requestData,
            ]);

            DB::commit();
            return redirect()->route('admin.settings.index')->with('success', 'Settings Updated Successfully');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }


    public function env($value)
    {
        try {

            $googleRecaptcha = $value['google_reCaptcha'] ?? null;
            if ($googleRecaptcha && is_array($googleRecaptcha)) {
                config([
                    'services.recaptcha.key' => $googleRecaptcha['site_key'],
                    'services.recaptcha.secret' => $googleRecaptcha['secret'],
                ]);
            } else {
                throw new Exception("Google recaptcha are not properly set in the database.");
            }
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }
}
