@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Create Survey')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <h4 class="mb-0">Create New Survey</h4>
  <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">Back to Surveys</a>
</div>

<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.surveys.store') }}" method="POST">
      @csrf

      <div class="mb-4">
        <label class="form-label" for="title">Survey Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
        @error('title')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
        @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
          <label class="form-check-label" for="is_active">Active</label>
        </div>
      </div>

      <div class="mb-4">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1">
          <label class="form-check-label" for="is_default">Set as Default (Homepage)</label>
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Create Survey</button>
    </form>
  </div>
</div>
@endsection
