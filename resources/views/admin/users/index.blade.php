@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Users Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <h4 class="mb-0">Users Management</h4>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
    <i class="bx bx-plus me-1"></i> Create User
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="card">
  <div class="card-datatable table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td>{{ $user->id }}</td>
            <td><strong>{{ $user->name }}</strong></td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
            <td>
              <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                  <i class="bx bx-edit"></i> Edit
                </a>
                @if($user->id !== session('user_id'))
                  <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                      <i class="bx bx-trash"></i> Delete
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">No users found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
    <div class="card-footer">
      {{ $users->links() }}
    </div>
  @endif
</div>
@endsection
