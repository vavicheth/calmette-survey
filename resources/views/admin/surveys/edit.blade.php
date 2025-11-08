@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Survey')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.css">
<style>
  .sortable-ghost {
    opacity: 0.4;
    background: #f8f9fa;
  }

  .sortable-chosen {
    background: #e7f3ff;
  }

  .sortable-drag {
    opacity: 1;
  }

  .drag-handle {
    cursor: grab !important;
    cursor: -webkit-grab !important;
    cursor: -moz-grab !important;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    touch-action: none;
    -webkit-touch-callout: none;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    min-height: 30px;
    padding: 5px;
  }

  .drag-handle:hover {
    background: rgba(0,0,0,0.05);
    border-radius: 4px;
  }

  .drag-handle:active {
    cursor: grabbing !important;
    cursor: -webkit-grabbing !important;
    cursor: -moz-grabbing !important;
    background: rgba(0,0,0,0.1);
  }

  .question-item {
    position: relative;
  }

  .question-item.sortable-chosen {
    transition: none !important;
  }

  .question-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  #questionsList {
    min-height: 50px;
  }
</style>
@endsection

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
        <div id="questionsList">
        @forelse($survey->questions as $question)
          <div class="border rounded p-3 mb-3 question-item" data-id="{{ $question->id }}">
            <div class="d-flex justify-content-between align-items-start">
              <div class="d-flex align-items-start flex-grow-1">
                <div class="drag-handle me-3">
                  <i class="bx bx-grid-vertical ri ri-menu-line icon-md" style="font-size: 1.5rem; color: #999; pointer-events: none;"></i>
                </div>
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
              </div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $question->id }}">
                  Edit
                </button>
                <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bx bx-trash"></i>
                    Delete
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <p class="text-muted">No questions added yet. Click "Add Question" to get started.</p>
        @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Question Modals -->
@foreach($survey->questions as $question)
<div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.questions.update', $question) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Question</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Question Text <span class="text-danger">*</span></label>
            <textarea class="form-control" name="question_text" rows="3" required>{{ $question->question_text }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Question Type <span class="text-danger">*</span></label>
            <select class="form-select edit-question-type" name="question_type" data-question-id="{{ $question->id }}" required>
              <option value="text" {{ $question->question_type === 'text' ? 'selected' : '' }}>Short Text</option>
              <option value="textarea" {{ $question->question_type === 'textarea' ? 'selected' : '' }}>Long Text</option>
              <option value="radio" {{ $question->question_type === 'radio' ? 'selected' : '' }}>Multiple Choice (Single)</option>
              <option value="checkbox" {{ $question->question_type === 'checkbox' ? 'selected' : '' }}>Multiple Choice (Multiple)</option>
              <option value="select" {{ $question->question_type === 'select' ? 'selected' : '' }}>Dropdown</option>
              <option value="rating" {{ $question->question_type === 'rating' ? 'selected' : '' }}>Rating (1-10)</option>
            </select>
          </div>

          <div class="mb-3 edit-options-container" id="editOptionsContainer{{ $question->id }}" style="display: {{ in_array($question->question_type, ['radio', 'checkbox', 'select']) ? 'block' : 'none' }};">
            <label class="form-label">Options (one per line)</label>
            <textarea class="form-control edit-options-input" id="editOptionsInput{{ $question->id }}" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3">{{ $question->options ? implode("\n", $question->options) : '' }}</textarea>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_required" value="1" id="editIsRequired{{ $question->id }}" {{ $question->is_required ? 'checked' : '' }}>
              <label class="form-check-label" for="editIsRequired{{ $question->id }}">Required</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Question</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endforeach

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
              <option value="rating">Rating (1-10)</option>
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
  // Wait for DOM to be fully loaded
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable for drag and drop
    const questionsList = document.getElementById('questionsList');

    if (questionsList) {
      console.log('Initializing Sortable on questionsList');
      console.log('Number of questions:', questionsList.querySelectorAll('.question-item').length);
      console.log('Number of drag handles:', questionsList.querySelectorAll('.drag-handle').length);

      const sortable = new Sortable(questionsList, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        draggable: '.question-item',
        forceFallback: true,
        fallbackTolerance: 3,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onChoose: function(evt) {
          console.log('✓ Item chosen (mousedown on handle):', evt.item.getAttribute('data-id'));
        },
        onStart: function(evt) {
          console.log('✓ Drag started on item:', evt.item.getAttribute('data-id'));
        },
        onMove: function(evt) {
          console.log('✓ Item is moving');
        },
        onEnd: function(evt) {
          console.log('✓ Drag ended. Old index:', evt.oldIndex, 'New index:', evt.newIndex);

          // Get the new order
          const items = questionsList.querySelectorAll('.question-item');
          const order = Array.from(items).map(item => item.getAttribute('data-id'));

          console.log('New order:', order);

          // Send AJAX request to update order
          fetch('{{ route('admin.questions.reorder', $survey) }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order: order })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              console.log('Order updated successfully');
            }
          })
          .catch(error => {
            console.error('Error updating order:', error);
          });
        }
      });

      console.log('Sortable initialized:', sortable);

      // Test: Add click event to drag handles to verify they're interactive
      const handles = questionsList.querySelectorAll('.drag-handle');
      handles.forEach((handle, index) => {
        handle.addEventListener('mousedown', function(e) {
          console.log('✓ Mousedown detected on drag handle #' + index);
        });
        handle.addEventListener('mouseup', function(e) {
          console.log('✓ Mouseup detected on drag handle #' + index);
        });
      });
    } else {
      console.error('questionsList element not found');
    }
  });

  // Add Question Modal - Question Type Change
  window.addEventListener('load', function() {
    const questionTypeEl = document.getElementById('questionType');
    if (questionTypeEl) {
      questionTypeEl.addEventListener('change', function() {
        const optionsContainer = document.getElementById('optionsContainer');
        const optionsInput = document.getElementById('optionsInput');
        const showOptions = ['radio', 'checkbox', 'select'].includes(this.value);

        optionsContainer.style.display = showOptions ? 'block' : 'none';
        optionsInput.required = showOptions;
      });
    }
  });

  // Add Question Modal - Form Submit
  window.addEventListener('load', function() {
    const addForm = document.querySelector('#addQuestionModal form');
    if (addForm) {
      addForm.addEventListener('submit', function(e) {
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
    }

    // Edit Question Modals - Question Type Change
    document.querySelectorAll('.edit-question-type').forEach(select => {
    select.addEventListener('change', function() {
      const questionId = this.getAttribute('data-question-id');
      const optionsContainer = document.getElementById('editOptionsContainer' + questionId);
      const optionsInput = document.getElementById('editOptionsInput' + questionId);
      const showOptions = ['radio', 'checkbox', 'select'].includes(this.value);

      optionsContainer.style.display = showOptions ? 'block' : 'none';
      if (optionsInput) {
        optionsInput.required = showOptions;
      }
    });
  });

  // Edit Question Modals - Form Submit
  document.querySelectorAll('[id^="editQuestionModal"] form').forEach(form => {
    form.addEventListener('submit', function(e) {
      const questionTypeSelect = this.querySelector('.edit-question-type');
      const questionType = questionTypeSelect.value;
      const questionId = questionTypeSelect.getAttribute('data-question-id');
      const optionsInput = document.getElementById('editOptionsInput' + questionId);

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
  });
});
</script>
@endsection
