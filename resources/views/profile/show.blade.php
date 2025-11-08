@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'My Profile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <h4 class="mb-0">My Profile</h4>
  <a href="{{ route('profile.edit') }}" class="btn btn-primary">
    <i class="bx bx-edit"></i> Edit Profile
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="row">
  <div class="col-md-8">
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="mb-0">Profile Information</h5>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-3">
            <strong>Name:</strong>
          </div>
          <div class="col-md-9">
            {{ $user->name }}
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-md-3">
            <strong>Email:</strong>
          </div>
          <div class="col-md-9">
            {{ $user->email }}
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-md-3">
            <strong>Member Since:</strong>
          </div>
          <div class="col-md-9">
            {{ $user->created_at->format('F d, Y') }}
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Security</h5>
      </div>
      <div class="card-body">
        <p class="mb-3">Keep your account secure by using a strong password.</p>
        <a href="{{ route('profile.change-password') }}" class="btn btn-warning">
          <i class="bx bx-lock"></i> Change Password
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
