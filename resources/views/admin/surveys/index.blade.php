@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Surveys Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <h4 class="mb-0">Surveys Management</h4>
  <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary">
    <i class="bx bx-plus me-1"></i> Create Survey
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-basic table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Questions</th>
          <th>Responses</th>
          <th>Status</th>
          <th>Default</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($surveys as $survey)
          <tr>
            <td>{{ $survey->id }}</td>
            <td><strong>{{ $survey->title }}</strong></td>
            <td>{{ $survey->questions_count }}</td>
            <td>{{ $survey->responses_count }}</td>
            <td>
              <form action="{{ route('admin.surveys.toggle-status', $survey) }}" method="POST" class="d-inline">
                @csrf
                <span class="badge bg-label-{{ $survey->is_active ? 'success' : 'secondary' }} cursor-pointer" onclick="this.closest('form').submit()">
                  {{ $survey->is_active ? 'Active' : 'Inactive' }}
                </span>
              </form>
            </td>
            <td>
              @if($survey->is_default)
                <span class="badge bg-label-primary">Default</span>
              @else
                <form action="{{ route('admin.surveys.set-default', $survey) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-primary">Set Default</button>
                </form>
              @endif
            </td>
            <td>{{ $survey->created_at->format('M d, Y') }}</td>
            <td>
              <div class="d-flex gap-1 flex-wrap">
                <a href="{{ route('admin.surveys.edit', $survey) }}" class="btn btn-sm btn-primary">
                  <i class="bx bx-edit"></i> Edit
                </a>
                <a href="{{ route('admin.statistics.index', $survey) }}" class="btn btn-sm btn-info">
                  <i class="bx bx-bar-chart"></i> Stats
                </a>
                <a href="{{ route('survey.show', $survey) }}" target="_blank" class="btn btn-sm btn-secondary">
                  <i class="bx bx-link-external"></i> View
                </a>
                <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this survey?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bx bx-trash"></i> Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center py-4">No surveys found. Create your first survey!</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    if ($('.datatables-basic').length) {
      $('.datatables-basic').DataTable({
        order: [[0, 'desc']]
      });
    }
  });
</script>
@endsection
