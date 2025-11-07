@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Thank You')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <div class="card">
        <div class="card-body text-center">
          <div class="app-brand justify-content-center mb-6">
            <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
          </div>

          <div class="mb-6">
            <div class="avatar avatar-xl mx-auto mb-4">
              <span class="avatar-initial rounded-circle bg-label-success">
                <i class="bx bx-check bx-lg"></i>
              </span>
            </div>
            <h4 class="mb-1">Thank You!</h4>
            <p class="mb-6">Your response has been recorded successfully.</p>
            <a href="{{ route('survey.index') }}" class="btn btn-primary">Take Another Survey</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
