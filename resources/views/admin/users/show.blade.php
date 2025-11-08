@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'User Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <h4 class="mb-0">User Details</h4>
  <div>
    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary me-2">
      <i class="bx bx-edit"></i> Edit
    </a>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row mb-4">
      <div class="col-md-3">
        <strong>ID:</strong>
      </div>
      <div class="col-md-9">
        {{ $user->id }}
      </div>
    </div>

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
        <strong>Created At:</strong>
      </div>
      <div class="col-md-9">
        {{ $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A' }}
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-3">
        <strong>Updated At:</strong>
      </div>
      <div class="col-md-9">
        {{ $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : 'N/A' }}
      </div>
    </div>
  </div>
</div>
@endsection
