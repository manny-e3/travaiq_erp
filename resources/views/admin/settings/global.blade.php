@extends('layouts.admin')

@section('title', 'Global System Settings')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Global System Settings</h3>
                            <div class="nk-block-des text-soft">
                                <p>Configure system-wide localization, security policies, site descriptors, and administrative contact configurations.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <form action="{{ route('admin.settings.global.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-gs">
                            
                            <!-- Site Configuration Card -->
                            <div class="col-lg-6">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="title text-primary"><em class="icon ni ni-globe me-2"></em>Site Metadata</h5>
                                        </div>
                                        @php
                                            $siteGroup = $settings['site'] ?? collect();
                                        @endphp
                                        <div class="form-group mt-3">
                                            <label class="form-label" for="site_name">Site Name</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $siteGroup->firstWhere('key', 'site_name')->value ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label" for="site_tagline">Site Tagline</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="site_tagline" name="site_tagline" value="{{ $siteGroup->firstWhere('key', 'site_tagline')->value ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Localization Configuration Card -->
                            <div class="col-lg-6">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="title text-success"><em class="icon ni ni-coins me-2"></em>Currency & Localization</h5>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label" for="currency_symbol">Currency Symbol</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select form-control" id="currency_symbol" name="currency_symbol" required>
                                                    @php
                                                        $locGroup = $settings['Localization'] ?? collect();
                                                        $currSym = $locGroup->firstWhere('key', 'currency_symbol')->value ?? '$';
                                                    @endphp
                                                    <option value="$" {{ $currSym == '$' ? 'selected' : '' }}>$ (USD / CAD / AUD)</option>
                                                    <option value="₦" {{ $currSym == '₦' ? 'selected' : '' }}>₦ (NGN - Nigerian Naira)</option>
                                                    <option value="€" {{ $currSym == '€' ? 'selected' : '' }}>€ (EUR - Euro)</option>
                                                    <option value="£" {{ $currSym == '£' ? 'selected' : '' }}>£ (GBP - British Pound)</option>
                                                    <option value="¥" {{ $currSym == '¥' ? 'selected' : '' }}>¥ (JPY / CNY)</option>
                                                    <option value="₵" {{ $currSym == '₵' ? 'selected' : '' }}>₵ (GHS - Ghanaian Cedi)</option>
                                                    <option value="KSh" {{ $currSym == 'KSh' ? 'selected' : '' }}>KSh (KES - Kenyan Shilling)</option>
                                                </select>
                                            </div>
                                            <span class="sub-text text-soft mt-1">This symbol will prefix all prices, salary fields, and invoice amounts.</span>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label" for="currency_code">Currency Code (ISO-3)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="currency_code" name="currency_code" value="{{ $locGroup->firstWhere('key', 'currency_code')->value ?? '' }}" placeholder="USD" required maxlength="3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Configuration Card -->
                            <div class="col-lg-6">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="title text-danger"><em class="icon ni ni-shield-star me-2"></em>Security & Authentication</h5>
                                        </div>
                                        
                                        @php
                                            $secGroup = $settings['Security'] ?? collect();
                                            $otpEnabled = ($secGroup->firstWhere('key', 'enable_otp')->value ?? '1') == '1';
                                            $loginEnabled = ($secGroup->firstWhere('key', 'login_enabled')->value ?? '1') == '1';
                                        @endphp
                                        <div class="form-group mt-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <label class="form-label mb-0" for="enable_otp">Require OTP on Login</label>
                                                    <div class="sub-text text-soft">Send one-time password verification to email upon authentication attempts.</div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="enable_otp" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="enable_otp" name="enable_otp" value="1" {{ $otpEnabled ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="enable_otp"></label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr class="preview-hr">

                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <label class="form-label mb-0" for="login_enabled">Non-Admin Login Status</label>
                                                    <div class="sub-text text-soft">Disable this option to block login portals for standard users while maintenance or audits are performed. Administrators can always log in.</div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="login_enabled" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="login_enabled" name="login_enabled" value="1" {{ $loginEnabled ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="login_enabled"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Branding & Appearance Card -->
                            <div class="col-lg-6">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="title text-warning"><em class="icon ni ni-paint-brush me-2"></em>Branding & Appearance</h5>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label" for="site_logo">Site Logo</label>
                                            <div class="form-control-wrap">
                                                <div class="form-file">
                                                    <input type="file" class="form-file-input" id="site_logo" name="site_logo" accept="image/*">
                                                    <label class="form-file-label" for="site_logo">Choose file</label>
                                                </div>
                                            </div>
                                            @if($logo = \App\Models\Setting::get('site_logo'))
                                                <div class="mt-2 p-2 bg-light border rounded text-center">
                                                     <img src="{{ asset($logo) }}" alt="Current Logo" style="max-height: 50px;">
                                                     <div class="sub-text text-soft mt-1">Current uploaded logo</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label" for="theme_color">Theme Primary Color</label>
                                            <div class="form-control-wrap d-flex align-items-center gap-2">
                                                <input type="color" class="form-control form-control-color" id="theme_color" name="theme_color" value="{{ \App\Models\Setting::get('theme_color', '#6576ff') }}" style="width: 60px; height: 38px; padding: 4px;">
                                                <span class="sub-text text-soft">Choose the accent color for dashboard items, links, and headers.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Emails / Notification Card -->
                            <div class="col-lg-6">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="title text-info"><em class="icon ni ni-mail me-2"></em>Departmental Contacts</h5>
                                        </div>
                                        @php
                                            $mailGroup = $settings['mail'] ?? collect();
                                        @endphp
                                        <div class="form-group mt-3">
                                            <label class="form-label" for="support_email">Support Desk Email</label>
                                            <div class="form-control-wrap">
                                                <input type="email" class="form-control" id="support_email" name="support_email" value="{{ $mailGroup->firstWhere('key', 'support_email')->value ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label" for="finance_email">Finance Desk Email</label>
                                            <div class="form-control-wrap">
                                                <input type="email" class="form-control" id="finance_email" name="finance_email" value="{{ $mailGroup->firstWhere('key', 'finance_email')->value ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-lg btn-primary"><em class="icon ni ni-save me-1"></em>Save All System Settings</button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
