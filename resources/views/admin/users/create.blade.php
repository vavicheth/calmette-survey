@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Create User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <h4 class="mb-0">Create New User</h4>
  <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
</div>

<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.users.store') }}" method="POST">
      @csrf

      <div class="mb-4">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
      </div>

      <button type="submit" class="btn btn-primary">Create User</button>
      <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</div>
@endsection
