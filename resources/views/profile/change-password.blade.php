@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Change Password')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <h4 class="mb-0">Change Password</h4>
  <a href="{{ route('profile.show') }}" class="btn btn-secondary">Back to Profile</a>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('profile.change-password.update') }}" method="POST">
          @csrf

          <div class="mb-4">
            <label class="form-label" for="current_password">Current Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required autofocus>
            @error('current_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Password must be at least 8 characters long.</div>
          </div>

          <div class="mb-4">
            <label class="form-label" for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
          </div>

          <button type="submit" class="btn btn-warning">Change Password</button>
          <a href="{{ route('profile.show') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
