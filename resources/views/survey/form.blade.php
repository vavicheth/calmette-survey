@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', $survey->title)

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <div class="card">
        <div class="card-body">
          <div class="app-brand-img text-center">
            <img class="mb-4" src="{{asset('assets/img/logo-text.png')}}" alt="Logo" height="50px" srcset="">
            <h4 class="text-primary">ការផ្តល់មតិយោបល់</h4>
          </div>
          <h4 class="mb-1">{{ $survey->title }}</h4>
          @if($survey->description)
            <p class="mb-6">{{ $survey->description }}</p>
          @endif

          @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <form action="{{ route('survey.submit', $survey) }}" method="POST" class="mb-4">
            @csrf

            @foreach($survey->questions as $question)
              <div class="mb-6">
                <label class="form-label" for="question-{{ $question->id }}">
                  {{ $question->question_text }}
                  @if($question->is_required)
                    <span class="text-danger">*</span>
                  @endif
                </label>

                @if($question->question_type === 'text')
                  <input
                    type="text"
                    class="form-control"
                    id="question-{{ $question->id }}"
                    name="answers[{{ $question->id }}]"
                    {{ $question->is_required ? 'required' : '' }}
                  >
                @elseif($question->question_type === 'textarea')
                  <textarea
                    class="form-control"
                    id="question-{{ $question->id }}"
                    name="answers[{{ $question->id }}]"
                    rows="4"
                    {{ $question->is_required ? 'required' : '' }}
                  ></textarea>
                @elseif($question->question_type === 'radio')
                  @foreach($question->options as $index => $option)
                    <div class="form-check mt-2">
                      <input
                        class="form-check-input"
                        type="radio"
                        name="answers[{{ $question->id }}]"
                        id="question-{{ $question->id }}-{{ $index }}"
                        value="{{ $option }}"
                        {{ $question->is_required ? 'required' : '' }}
                      >
                      <label class="form-check-label" for="question-{{ $question->id }}-{{ $index }}">
                        {{ $option }}
                      </label>
                    </div>
                  @endforeach
                @elseif($question->question_type === 'checkbox')
                  @foreach($question->options as $index => $option)
                    <div class="form-check mt-2">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        name="answers[{{ $question->id }}][]"
                        id="question-{{ $question->id }}-{{ $index }}"
                        value="{{ $option }}"
                      >
                      <label class="form-check-label" for="question-{{ $question->id }}-{{ $index }}">
                        {{ $option }}
                      </label>
                    </div>
                  @endforeach
                @elseif($question->question_type === 'select')
                  <select
                    class="form-select"
                    id="question-{{ $question->id }}"
                    name="answers[{{ $question->id }}]"
                    {{ $question->is_required ? 'required' : '' }}
                  >
                    <option value="">Select an option</option>
                    @foreach($question->options as $option)
                      <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                  </select>
                @elseif($question->question_type === 'rating')
                  <div class="d-flex gap-2 mt-2 flex-wrap">
                    @for($i = 1; $i <= 10; $i++)
                      <div class="form-check form-check-inline">
                        <input
                          class="form-check-input"
                          type="radio"
                          name="answers[{{ $question->id }}]"
                          id="question-{{ $question->id }}-{{ $i }}"
                          value="{{ $i }}"
                          {{ $question->is_required ? 'required' : '' }}
                        >
                        <label class="form-check-label" for="question-{{ $question->id }}-{{ $i }}">
                          {{ $i }}
                        </label>
                      </div>
                    @endfor
                  </div>
                @endif

                @error('answers.' . $question->id)
                  <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
              </div>
            @endforeach

            <button type="submit" class="btn btn-primary d-grid w-100">Submit Survey</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
