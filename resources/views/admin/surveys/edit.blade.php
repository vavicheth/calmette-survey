@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Survey')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <h4 class="mb-0">Edit Survey</h4>
      <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">Back to Surveys</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="card mb-6">
      <div class="card-header">
        <h5 class="mb-0">Survey Details</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.surveys.update', $survey) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="mb-4">
            <label class="form-label" for="title">Survey Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $survey->title) }}" required>
            @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $survey->description) }}</textarea>
          </div>

          <div class="mb-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $survey->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active</label>
            </div>
          </div>

          <div class="mb-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $survey->is_default) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_default">Set as Default (Homepage)</label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Update Survey</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Questions</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
          <i class="bx bx-plus"></i> Add Question
        </button>
      </div>
      <div class="card-body">
        @forelse($survey->questions as $question)
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between">
              <div class="flex-grow-1">
                <p class="mb-1"><strong>{{ $question->question_text }}</strong></p>
                <span class="badge bg-label-info">{{ ucfirst($question->question_type) }}</span>
                @if($question->is_required)
                  <span class="badge bg-label-danger">Required</span>
                @endif
                @if($question->options)
                  <div class="mt-2 text-muted small">
                    Options: {{ implode(', ', $question->options) }}
                  </div>
                @endif
              </div>
              <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
            </div>
          </div>
        @empty
          <p class="text-muted">No questions added yet. Click "Add Question" to get started.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.questions.store', $survey) }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Question</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Question Text <span class="text-danger">*</span></label>
            <textarea class="form-control" name="question_text" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Question Type <span class="text-danger">*</span></label>
            <select class="form-select" name="question_type" id="questionType" required>
              <option value="text">Short Text</option>
              <option value="textarea">Long Text</option>
              <option value="radio">Multiple Choice (Single)</option>
              <option value="checkbox">Multiple Choice (Multiple)</option>
              <option value="select">Dropdown</option>
              <option value="rating">Rating (1-5)</option>
            </select>
          </div>

          <div class="mb-3" id="optionsContainer" style="display: none;">
            <label class="form-label">Options (one per line)</label>
            <textarea class="form-control" id="optionsInput" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_required" value="1" id="isRequired">
              <label class="form-check-label" for="isRequired">Required</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Question</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('page-script')
<script>
  document.getElementById('questionType').addEventListener('change', function() {
    const optionsContainer = document.getElementById('optionsContainer');
    const optionsInput = document.getElementById('optionsInput');
    const showOptions = ['radio', 'checkbox', 'select'].includes(this.value);

    optionsContainer.style.display = showOptions ? 'block' : 'none';
    optionsInput.required = showOptions;
  });

  document.querySelector('#addQuestionModal form').addEventListener('submit', function(e) {
    const questionType = document.getElementById('questionType').value;
    const optionsInput = document.getElementById('optionsInput');

    if (['radio', 'checkbox', 'select'].includes(questionType)) {
      const options = optionsInput.value.split('\n').filter(opt => opt.trim());

      // Remove existing hidden inputs
      this.querySelectorAll('input[name="options[]"]').forEach(el => el.remove());

      // Add new hidden inputs for each option
      options.forEach(option => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'options[]';
        input.value = option.trim();
        this.appendChild(input);
      });
    }
  });
</script>
@endsection
