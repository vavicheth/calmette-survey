@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'No Survey Available')

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
              <span class="avatar-initial rounded-circle bg-label-warning">
                <i class="bx bx-info-circle bx-lg"></i>
              </span>
            </div>
            <h4 class="mb-1">No Survey Available</h4>
            <p class="mb-0">There are currently no active surveys. Please check back later.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
