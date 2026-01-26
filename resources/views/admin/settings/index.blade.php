@extends('layouts.admin', ['title' => 'Settings'])

@use('App\Models\Settings')
@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Apps', 'title' => 'Settings'])
    @include('admin.partials.alerts')
    <div class="row">
        <div class="col-md-12 box-col-12">
            <form method="POST" class="row custom-input" id="settingsForm"
                action="{{ route('admin.update.settings', $settingsId) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="tab-content w-100" id="v-pills-tabContent">
                    <div class="tab-pane main fade show active" id="General" role="tabpanel"
                        aria-labelledby="v-pills-settings-tab" tabindex="4">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="image" class="col-md-2">Logo<span class="text-danger">*</span></label>
                                    <div class="col-md-10 mb-3">
                                        <input class="form-control" type="file" id="general[logo]" name="general[logo]">
                                        @error('general[logo]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <span class="help-text">*Upload image size 853×250px
                                            recommended</span>
                                    </div>
                                </div>
                                @isset($settings['general']['logo'])
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-10 mb-3">
                                                <div class="image-list d-inline-block">
                                                    <div class="image-list-detail">
                                                        <div class="position-relative">
                                                            <img src="{{ $settings['general']['logo'] }}" height="40px"
                                                                alt="Logo" class="image-list-item">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endisset
                                <div class="form-group row">
                                    <label for="image" class="col-md-2">Logo (Small)<span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-10 mb-3">
                                        <input class="form-control" type="file" id="general[logo_sm]"
                                            name="general[logo_sm]">
                                        @error('general[logo_sm]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <span class="help-text">*Upload image size 300×100px recommended</span>
                                    </div>
                                </div>
                                @isset($settings['general']['logo_sm'])
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-10 mb-3">
                                                <div class="image-list d-inline-block">
                                                    <div class="image-list-detail">
                                                        <div class="position-relative">
                                                            <img src="{{ asset($settings['general']['logo_sm']) }}"
                                                                height="40px" id="{{ $settings['general']['logo_sm'] }}"
                                                                alt="Logo Small" class="image-list-item">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endisset
                                <div class="form-group row">
                                    <label for="image" class="col-md-2">Favicon<span class="text-danger">*</span></label>
                                    <div class="col-md-10 mb-3">
                                        <input class="form-control" type="file" id="general[favicon]"
                                            name="general[favicon]">
                                        @error('general[favicon]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <span class="help-text">*Upload image size 172x172px
                                            recommended</span>
                                    </div>
                                </div>
                                @isset($settings['general']['favicon'])
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-10 mb-3">
                                                <div class="image-list">
                                                    <div class="image-list-detail">
                                                        <div class="position-relative">
                                                            <img src="{{ asset($settings['general']['favicon']) }}"
                                                                height="40px" id="{{ $settings['general']['favicon'] }}"
                                                                alt="favicon" class="image-list-item">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endisset
                                <div class="form-group row">
                                    <label class="col-md-2" for="general[site_name]">Site Name<span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-10 mb-3">
                                        <input class="form-control" type="text" id="general[site_name]"
                                            name="general[site_name]"
                                            value="{{ $settings['general']['site_name'] ?? old('site_name') }}"
                                            placeholder="Enter Site Name">
                                        @error('general[site_name]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2" for="general[footer]">Footer<span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-10 mb-3">
                                        <input class="form-control" type="text" id="general[footer]"
                                            name="general[footer]"
                                            value="{{ $settings['general']['footer'] ?? old('footer') }}"
                                            placeholder="Enter Footer">
                                        @error('general[footer]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary spinner-btn" type="submit">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane main fade" id="Google_Recaptcha" role="tabpanel"
                        aria-labelledby="v-pills-settings-tab" tabindex="4">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-2" for="google_reCaptcha[secret]">Secret Key<span
                                            class="text-danger">
                                            *</span></label>
                                    <div class="col-md-10 mb-3">
                                        <input class="form-control" type="password" id="google_reCaptcha[secret]"
                                            name="google_reCaptcha[secret]"
                                            value="{{ $settings['google_reCaptcha']['secret'] ?? old('secret') }}"
                                            placeholder="Enter Secret Key">
                                        @error('google_reCaptcha[secret]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2" for="google_reCaptcha[site_key]">Site
                                        Key<span class="text-danger">*</span></label>
                                    <div class="col-md-10 mb-3">
                                        <input class="form-control" type="password" id="google_reCaptcha[site_key]"
                                            name="google_reCaptcha[site_key]"
                                            value="{{ $settings['google_reCaptcha']['site_key'] ?? old('site_key') }}"
                                            placeholder="Enter Site Key">
                                        @error('google_reCaptcha[site_key]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary spinner-btn" type="submit">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane main fade" id="Social_Login" role="tabpanel"
                        aria-labelledby="v-pills-settings-tab" tabindex="4">
                        <div class="card">
                            <div class="card-body">
                                <ul class="simple-wrapper nav nav-tabs border-tab border-1 nav-primary mb-5"
                                    id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="google_tab" data-bs-toggle="pill" href="#google"
                                            role="tab" aria-controls="google" aria-selected="true">Google</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="facebook_tab" data-bs-toggle="pill" href="#facebook"
                                            role="tab" aria-controls="facebook" aria-selected="false">Facebook</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="google" role="tabpanel"
                                        aria-labelledby="google_tab">
                                        <div class="form-group row">
                                            <label class="col-md-2" for="social_login[google][google_client_id]">
                                                Client
                                                Id<span class="text-danger">*</span></label>
                                            <div class="col-md-10 mb-3">
                                                <input class="form-control" type="password"
                                                    id="social_login[google][google_client_id]"
                                                    name="social_login[google][google_client_id]"
                                                    value="{{ $settings['social_login']['google']['google_client_id'] ?? old('social_login[google][google_client_id]') }}"
                                                    placeholder="Enter Client Id">
                                                @error('social_login[google][google_client_id]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-2" for="social_login[google][google_client_secret]">
                                                Client
                                                Secret<span class="text-danger">*</span></label>
                                            <div class="col-md-10 mb-3">
                                                <input class="form-control" type="password"
                                                    id="social_login[google][google_client_secret]"
                                                    name="social_login[google][google_client_secret]"
                                                    value="{{ $settings['social_login']['google']['google_client_secret'] ?? old('social_login[google][google_client_secret]') }}"
                                                    placeholder="Enter Client Secret">
                                                @error('social_login[google][google_client_secret]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-2" for="social_login[google][google_redirect_url]">
                                                Redirect
                                                Url<span class="text-danger">*</span></label>
                                            <div class="col-md-10 mb-3">
                                                <input class="form-control" type="password"
                                                    id="social_login[google][google_redirect_url]"
                                                    name="social_login[google][google_redirect_url]"
                                                    value="{{ $settings['social_login']['google']['google_redirect_url'] ?? old('social_login[google][google_redirect_url]') }}"
                                                    placeholder="Enter Redirect Url">
                                                @error('social_login[google][google_redirect_url]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-2"
                                                for="social_login[google][google_login_status]">Status</label>
                                            <div class="col-md-10 mb-3">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($settings['social_login']['google']['google_login_status']))
                                                            <input class="form-control" type="hidden"
                                                                name="social_login[google][google_login_status]"
                                                                value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="social_login[google][google_login_status]"
                                                                value="1"
                                                                {{ $settings['social_login']['google']['google_login_status'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="social_login[google][google_login_status]"
                                                                value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="social_login[google][google_login_status]"
                                                                value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary spinner-btn" type="submit">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    {{-- <script src="{{ asset('admin/assets/js/bookmark/jquery.validate.min.js') }}"></script> --}}
    <script>
        $(document).ready(function() {

            "use strict";

            function addImageMimeValidation(methodName, mimeType, errorMessage) {
                $.validator.addMethod(methodName, function(value, element) {
                    const file = element.files && element.files[0];
                    if (file) {
                        const mime = file.type;
                        return mime === mimeType;
                    }
                    return true;
                }, errorMessage);
            }

            // Add image MIME validation methods
            addImageMimeValidation("imageMime", "image/png",
                "Please enter a value with a valid mimetype. (JPG, PNG, JPEG).");

            $('#settingsForm').validate({
                ignore: [],
                rules: {
                    'general[logo]': {
                        required: function() {
                            return {{ isset($settings['general']['logo']) ? 'false' : 'true' }};
                        },
                        imageMime: true
                    },
                    'general[logo_sm]': {
                        required: function() {
                            return {{ isset($settings['general']['logo_sm']) ? 'false' : 'true' }};
                        },
                        imageMime: true
                    },
                    'general[favicon]': {
                        required: function() {
                            return {{ isset($settings['general']['favicon']) ? 'false' : 'true' }};
                        },
                        imageMime: true
                    },
                    'general[site_name]': "required",
                    'general[footer]': "required",
                    'google_reCaptcha[site_key]': "required",
                    'google_reCaptcha[secret]': "required",
                    'social_login[google][google_client_id]': "required",
                    'social_login[google][google_client_secret]': "required",
                    'social_login[google][google_redirect_url]': "required",
                    'social_login[facebook][facebook_client_id]': "required",
                    'social_login[facebook][facebook_client_secret]': "required",
                    'social_login[facebook][facebook_redirect_url]': "required",
                },
                invalidHandler: function(event, validator) {
                    let invalidMainTabs = [];
                    let invalidSubTabs = new Map(); // Store invalid sub-tabs per main tab

                    // Collect all invalid tabs
                    $.each(validator.errorList, function(index, error) {
                        const subTabId = $(error.element).closest('.tab-pane').attr('id');
                        const mainTabId = $("#" + subTabId).closest('.main').attr('id');

                        if (mainTabId && !invalidMainTabs.includes(mainTabId)) {
                            invalidMainTabs.push(mainTabId);
                        }

                        if (subTabId) {
                            if (!invalidSubTabs.has(mainTabId)) {
                                invalidSubTabs.set(mainTabId, []);
                            }
                            if (!invalidSubTabs.get(mainTabId).includes(subTabId)) {
                                invalidSubTabs.get(mainTabId).push(subTabId);
                            }
                        }
                    });

                    if (invalidMainTabs.length > 0) {
                        let firstMainTab = invalidMainTabs[0];
                        let firstSubTab = invalidSubTabs.get(firstMainTab)?.[0];

                        // If no invalid sub-tabs exist, activate the first available sub-tab inside this main tab
                        if (!firstSubTab) {
                            firstSubTab = $("#" + firstMainTab).find(".tab-pane").first().attr('id');
                        }

                        activateTab(firstMainTab, firstSubTab);
                    }

                    function activateTab(mainTabId, subTabId) {
                        // Reset only main tabs, sub-tabs are handled separately
                        $(".nav-link.main.active").removeClass("active");
                        $(".tab-pane.main.show").removeClass("show active");

                        // Activate the first invalid main tab
                        if (mainTabId) {
                            $("#" + mainTabId + "_tab").addClass("active");
                            $("#" + mainTabId).addClass("show active");
                        }

                        // Activate the first invalid sub-tab OR the first available sub-tab
                        if (subTabId) {
                            $("#" + subTabId + "_tab").tab(
                                "show"); // Bootstrap handles proper switching
                        }
                    }
                }
            });

            function isLogo() {
                @if (isset($settings['general']['logo']))
                    return false;
                @else
                    return true;
                @endif
            }

            function isLogoSm() {
                @if (isset($settings['general']['logo_sm']))
                    return false;
                @else
                    return true;
                @endif
            }

            function isFavicon() {
                @if (isset($settings['general']['favicon']))
                    return false;
                @else
                    return true;
                @endif
            }
        });
    </script>
@endsection
