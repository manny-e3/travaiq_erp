@extends('layouts.admin-auth')

@section('title', 'Sign In')

@section('content')
<div class="auth-split-wrapper">
    <!-- Visual Side -->
    <div class="auth-split-visual">
        <div class="auth-split-content">
            <span class="badge mb-3 fw-semibold text-uppercase" style="letter-spacing: 0.12em; font-size: 0.72rem; padding: 0.45rem 0.9rem; border-radius: 4px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff;">Enterprise Resource Planning</span>
            @if($logo = \App\Models\Setting::get('site_logo'))
                <img class="logo-img logo-img-lg mb-4" src="{{ asset($logo) }}" alt="logo" style="max-height: 60px; width: auto; object-fit: contain;">
            @else
                <h1 class="brand-logo-text font-serif mb-3">{{ \App\Models\Setting::get('site_name', config('app.name', 'BPA Enterprise ERP')) }}</h1>
            @endif
            @if($tagline = \App\Models\Setting::get('site_tagline'))
                <p class="fs-16px italic font-serif mb-4" style="color: rgba(255, 255, 255, 0.88); line-height: 1.6;">"{{ $tagline }}"</p>
            @else
                <p class="fs-16px italic font-serif mb-4" style="color: rgba(255, 255, 255, 0.88); line-height: 1.6;">"Intelligent Enterprise Resource Planning & Business Process Automation"</p>
            @endif
            <div style="width: 48px; height: 4px; background: var(--primary-color, #6576ff); border-radius: 2px;"></div>
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-split-form-side">
        <div class="auth-form-inner">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h2 class="nk-block-title font-serif">Sign In</h2>
                    <p class="text-soft fs-15px">Sign in to access your enterprise dashboard and business operations.</p>
                </div>
            </div>
            
            <form id="login-form" action="{{ route('login.post') }}" method="POST" class="mt-5">
                @csrf
                <div class="form-group mb-4">
                    <label class="form-label">Email Address</label>
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-right">
                            <em class="icon ni ni-mail"></em>
                        </div>
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="Enter your email" value="{{ old('email') }}" required>
                    </div>
                    @error('email') <span class="text-danger mt-1 d-block fs-12px">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">Password</label>
                        <a href="{{ route('password.request') }}" class="link link-primary fw-bold" style="color: var(--primary-color) !important;">Forgot Password?</a>
                    </div>
                    <div class="form-control-wrap">
                        <a href="#" class="form-control-icon form-control-icon-right passcode-switch lg" data-target="password">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>
                        <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Enter your password" required>
                    </div>
                    @error('password') <span class="text-danger mt-1 d-block fs-12px">{{ $message }}</span> @enderror
                </div>

                <div class="form-group pt-2">
                    <button type="submit" id="login-submit-btn" class="btn btn-lg btn-primary btn-block">
                        <span id="btn-text">Sign In</span>
                        <em id="btn-icon" class="icon ni ni-arrow-right"></em>
                    </button>
                </div>
            </form>

            
        </div>
    </div>
</div>

<style>
.btn-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    vertical-align: text-bottom;
    border: 0.15em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: btn-spinner-animation .75s linear infinite;
}
@keyframes btn-spinner-animation {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.getElementById('login-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('login-submit-btn');
    const text = document.getElementById('btn-text');
    const icon = document.getElementById('btn-icon');
    
    if (btn) {
        // Use setTimeout to ensure form submits before button is disabled
        setTimeout(() => {
            btn.disabled = true;
        }, 0);
        
        if (text) text.textContent = 'Signing In...';
        if (icon) {
            icon.className = 'btn-spinner';
        }
    }
});
</script>
@endsection
