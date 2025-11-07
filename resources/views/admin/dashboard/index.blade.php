@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('content')
<h4 class="mb-6">Dashboard Overview</h4>

<!-- Statistics Cards -->
<div class="row mb-6">
  <!-- Total Surveys -->
  <div class="col-xl-3 col-lg-6 col-md-6 mb-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Surveys</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $totalSurveys }}</h4>
              <span class="text-success">({{ $activeSurveys }} active)</span>
            </div>
            <small class="mb-0">All surveys created</small>
          </div>
          <div class="avatar avatar-lg">
            <div class="avatar-initial bg-label-primary rounded">
              <i class="icon-base ri ri-file-list-3-line icon-32px"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Questions -->
  <div class="col-xl-3 col-lg-6 col-md-6 mb-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Questions</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $totalQuestions }}</h4>
            </div>
            <small class="mb-0">Across all surveys</small>
          </div>
          <div class="avatar avatar-lg">
            <div class="avatar-initial bg-label-success rounded">
              <i class="icon-base ri ri-questionnaire-line icon-32px"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Responses -->
  <div class="col-xl-3 col-lg-6 col-md-6 mb-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Responses</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $totalResponses }}</h4>
            </div>
            <small class="mb-0">All time responses</small>
          </div>
          <div class="avatar avatar-lg">
            <div class="avatar-initial bg-label-info rounded">
              <i class="icon-base ri ri-bar-chart-box-line icon-32px"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Responses (Last 30 Days) -->
  <div class="col-xl-3 col-lg-6 col-md-6 mb-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <span class="text-heading">Recent Responses</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $recentResponses }}</h4>
            </div>
            <small class="mb-0">Last 30 days</small>
          </div>
          <div class="avatar avatar-lg">
            <div class="avatar-initial bg-label-warning rounded">
              <i class="icon-base ri ri-line-chart-line icon-32px"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row mb-6">
  <!-- Response Trend Chart -->
  <div class="col-lg-8 mb-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Response Trend</h5>
        <small class="text-muted">Last 14 days</small>
      </div>
      <div class="card-body">
        <canvas id="responseTrendChart" style="height: 300px;"></canvas>
      </div>
    </div>
  </div>

  <!-- Average Responses Card -->
  <div class="col-lg-4 mb-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Quick Stats</h5>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <div class="d-flex align-items-center mb-2">
            <h3 class="mb-0 me-2">{{ $avgResponsesPerSurvey }}</h3>
            <span class="text-muted">responses per survey</span>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $avgResponsesPerSurvey * 10) }}%"></div>
          </div>
        </div>

        <div class="mb-4">
          <small class="text-muted">Active Surveys</small>
          <div class="d-flex align-items-center">
            <h4 class="mb-0 me-2">{{ $activeSurveys }}</h4>
            <span class="text-muted">/ {{ $totalSurveys }}</span>
          </div>
          <div class="progress mt-2" style="height: 8px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalSurveys > 0 ? ($activeSurveys / $totalSurveys * 100) : 0 }}%"></div>
          </div>
        </div>

        <div>
          <small class="text-muted">Response Rate</small>
          <div class="d-flex align-items-center">
            <h4 class="mb-0 me-2">{{ $totalQuestions > 0 ? round(($totalResponses / $totalQuestions) * 100, 1) : 0 }}%</h4>
          </div>
          <div class="progress mt-2" style="height: 8px;">
            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalQuestions > 0 ? min(100, ($totalResponses / $totalQuestions) * 100) : 0 }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tables Row -->
<div class="row">
  <!-- Popular Surveys -->
  <div class="col-lg-6 mb-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Most Popular Surveys</h5>
        <a href="{{ route('admin.surveys.index') }}" class="btn btn-sm btn-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-borderless">
            <thead>
              <tr>
                <th>Survey</th>
                <th class="text-end">Responses</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($popularSurveys as $survey)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                          <i class="icon-base ri ri-file-list-line icon-20px"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-0">{{ Str::limit($survey->title, 30) }}</h6>
                        <small class="text-muted">{{ $survey->questions->count() }} questions</small>
                      </div>
                    </div>
                  </td>
                  <td class="text-end">
                    <span class="badge bg-label-primary">{{ $survey->responses_count }}</span>
                  </td>
                  <td>
                    <a href="{{ route('admin.statistics.index', $survey) }}" class="btn btn-sm btn-icon btn-text-secondary">
                      <i class="icon-base ri ri-arrow-right-s-line icon-20px"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">No surveys yet</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Responses -->
  <div class="col-lg-6 mb-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Recent Responses</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-borderless">
            <thead>
              <tr>
                <th>Survey</th>
                <th>Date</th>
                <th>IP</th>
              </tr>
            </thead>
            <tbody>
              @forelse($latestResponses as $response)
                <tr>
                  <td>
                    <h6 class="mb-0">{{ Str::limit($response->survey->title, 25) }}</h6>
                  </td>
                  <td>
                    <small class="text-muted">{{ $response->created_at->format('d/m/Y H:i') }}</small>
                  </td>
                  <td>
                    <small class="text-muted">{{ $response->respondent_ip }}</small>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">No responses yet</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  // Response Trend Chart
  const trendCtx = document.getElementById('responseTrendChart');
  if (trendCtx) {
    new Chart(trendCtx, {
      type: 'line',
      data: {
        labels: @json($dates),
        datasets: [{
          label: 'Responses',
          data: @json($dailyResponses),
          backgroundColor: 'rgba(105, 108, 255, 0.1)',
          borderColor: 'rgba(105, 108, 255, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: 'rgba(105, 108, 255, 1)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: '#fff',
            titleColor: '#566a7f',
            bodyColor: '#566a7f',
            borderColor: '#ddd',
            borderWidth: 1,
            padding: 10
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            },
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });
  }
</script>
@endsection
