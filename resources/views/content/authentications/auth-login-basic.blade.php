@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login Basic - Pages')

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
  <div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
      <div class="authentication-inner py-6">
        <!-- Login -->
        <div class="card p-md-7 p-1">
          <!-- Logo -->
          <div class="app-brand justify-content-center mt-5">
            <a href="{{ url('/') }}" class="app-brand-link gap-2">
              <span class="app-brand-logo demo">@include('_partials.macros')</span>
              <span class="app-brand-text demo text-heading fw-semibold">{{ config('variables.templateName') }}</span>
            </a>
          </div>
          <!-- /Logo -->

          <div class="card-body mt-1">
            <h4 class="mb-1">Welcome to {{ config('variables.templateName') }}! 👋</h4>
            <p class="mb-5">Please sign-in to your account to manage surveys</p>

            @if(session('error'))
              <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            @if(session('success'))
              <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            <div class="alert alert-info" role="alert">
              <strong>Demo Credentials:</strong><br>
              Email: admin@survey.com<br>
              Password: admin123
            </div>

            <form id="loginForm" class="mb-5" action="{{ route('auth.login') }}" method="POST">
              @csrf
              <div class="form-floating form-floating-outline mb-5">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                  placeholder="Enter your email" value="{{ old('email', 'admin@survey.com') }}" required autofocus />
                <label for="email">Email</label>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-5">
                <div class="form-password-toggle">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                        aria-describedby="password" required />
                      <label for="password">Password</label>
                    </div>
                    <span class="input-group-text cursor-pointer"><i
                        class="icon-base ri ri-eye-off-line icon-20px"></i></span>
                  </div>
                  @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="mb-5">
                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
              </div>
            </form>

            <p class="text-center mb-5">
              <a href="{{ route('survey.index') }}">
                <span>← Back to Survey</span>
              </a>
            </p>
          </div>
        </div>
        <!-- /Login -->
        <img alt="mask"
          src="{{ asset('assets/img/illustrations/auth-basic-login-mask-' . $configData['theme'] . '.png') }}"
          class="authentication-image d-none d-lg-block"
          data-app-light-img="illustrations/auth-basic-login-mask-light.png"
          data-app-dark-img="illustrations/auth-basic-login-mask-dark.png" />
      </div>
    </div>
  </div>
@endsection
