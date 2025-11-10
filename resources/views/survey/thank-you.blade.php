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
            <h4 class="text-primary">ការផ្តល់មតិយោបល់</h4>
            {{--            <span class="app-brand-text demo text-heading fw-bold">--}}
            {{--              {{ \Illuminate\Support\Str::upper(env("APP_NAME")) }}--}}

            {{--            </span>--}}
          </div>

          <div class="mb-6">
            <div class="avatar avatar-xl mx-auto mb-4">
              <span class="avatar-initial rounded-circle bg-label-success">
                <i class="ri ri-checkbox-circle-line bx-xl"></i>
              </span>
            </div>
            <h4 class="mb-1">សូមអរគុណ!</h4>
            <p class="mb-6">មតិយោបល់របស់លោកអ្នកត្រូវបានរក្សាទុកជោគជ័យ</p>
{{--            <p class="mb-6">ប្រសិនបើលោកអ្នកចង់ផ្តល់មតិយោបល់បន្ថែមទៀត សូមចុចប៊ូតុងខាងក្រោម៖</p>--}}
            <a href="{{ route('survey.index') }}" class="btn btn-primary">ផ្តល់មតិយោបល់បន្ថែមថ្មី</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
