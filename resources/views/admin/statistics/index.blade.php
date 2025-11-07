@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Survey Statistics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <h4 class="mb-0">Statistics: {{ $survey->title }}</h4>
  <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">Back to Surveys</a>
</div>

<!-- Date Filter & Export Buttons -->
<div class="card mb-6">
  <div class="card-body">
    <form method="GET" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Start Date</label>
        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">End Date</label>
        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
      </div>
      <div class="col-md-4 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.statistics.export-excel', ['survey' => $survey, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
          <i class="bx bx-download"></i> Excel
        </a>
        <a href="{{ route('admin.statistics.export-pdf', ['survey' => $survey, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger">
          <i class="bx bx-download"></i> PDF
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Summary Cards -->
<div class="row mb-6">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar avatar-lg bg-label-primary rounded me-4">
            <i class="bx bx-bar-chart-alt-2 bx-lg"></i>
          </div>
          <div class="card-info">
            <h5 class="mb-0">{{ $totalResponses }}</h5>
            <small>Total Responses</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar avatar-lg bg-label-success rounded me-4">
            <i class="bx bx-question-mark bx-lg"></i>
          </div>
          <div class="card-info">
            <h5 class="mb-0">{{ $survey->questions->count() }}</h5>
            <small>Total Questions</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar avatar-lg bg-label-info rounded me-4">
            <i class="bx bx-calendar bx-lg"></i>
          </div>
          <div class="card-info">
            <h5 class="mb-0">{{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }}</h5>
            <small>Days in Range</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts -->
@foreach($statistics as $index => $stat)
  @if(isset($stat['breakdown']))
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $stat['question'] }}</h5>
        <span class="badge bg-label-info">{{ ucfirst($stat['type']) }}</span>
      </div>
      <div class="card-body">
        <p class="mb-3">Total Answers: <strong>{{ $stat['total_responses'] }}</strong></p>

        @if(isset($stat['average']))
          <div class="alert alert-info">
            <strong>Average Rating:</strong> {{ $stat['average'] }} / 5
          </div>
        @endif

        <!-- Chart Type Switcher -->
        <div class="mb-3">
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary chart-type-btn active" data-chart-id="{{ $stat['question_id'] }}" data-chart-type="bar">
              <i class="bx bx-bar-chart"></i> Bar
            </button>
            <button type="button" class="btn btn-outline-primary chart-type-btn" data-chart-id="{{ $stat['question_id'] }}" data-chart-type="line">
              <i class="bx bx-line-chart"></i> Line
            </button>
            <button type="button" class="btn btn-outline-primary chart-type-btn" data-chart-id="{{ $stat['question_id'] }}" data-chart-type="pie">
              <i class="bx bx-pie-chart-alt-2"></i> Pie
            </button>
            <button type="button" class="btn btn-outline-primary chart-type-btn" data-chart-id="{{ $stat['question_id'] }}" data-chart-type="doughnut">
              <i class="bx bx-doughnut-chart"></i> Doughnut
            </button>
          </div>
        </div>

        <!-- Chart Canvas -->
        <div class="mb-4">
          <canvas id="chart-{{ $stat['question_id'] }}" style="max-height: 350px;"></canvas>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Answer</th>
                <th>Count</th>
                <th>Percentage</th>
              </tr>
            </thead>
            <tbody>
              @foreach($stat['breakdown'] as $answer => $count)
                <tr>
                  <td>{{ $answer }}</td>
                  <td>{{ $count }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <span class="me-2">{{ $stat['total_responses'] > 0 ? round(($count / $stat['total_responses']) * 100, 1) : 0 }}%</span>
                      <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $stat['total_responses'] > 0 ? round(($count / $stat['total_responses']) * 100, 1) : 0 }}%"
                             aria-valuenow="{{ $count }}" aria-valuemin="0" aria-valuemax="{{ $stat['total_responses'] }}">
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endif

  @if(isset($stat['responses']) && count($stat['responses']) > 0)
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="mb-0">{{ $stat['question'] }}</h5>
        <span class="badge bg-label-info">{{ ucfirst($stat['type']) }}</span>
      </div>
      <div class="card-body">
        <p class="mb-3">Total Answers: <strong>{{ $stat['total_responses'] }}</strong></p>
        <div class="alert alert-info">
          <strong>Sample Responses:</strong>
          <ul class="mb-0 mt-2">
            @foreach($stat['responses'] as $response)
              <li>{{ $response }}</li>
            @endforeach
          </ul>
          @if($stat['total_responses'] > 10)
            <small class="text-muted">Showing 10 of {{ $stat['total_responses'] }} responses</small>
          @endif
        </div>
      </div>
    </div>
  @endif
@endforeach

<!-- Recent Responses -->
@if($totalResponses > 0)
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Recent Responses</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>ID</th>
              <th>Date</th>
              <th>IP Address</th>
              <th>Answers</th>
            </tr>
          </thead>
          <tbody>
            @foreach($responses->take(10) as $response)
              <tr>
                <td>#{{ $response->id }}</td>
                <td>{{ $response->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $response->respondent_ip }}</td>
                <td>{{ $response->answers->count() }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endif
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  const chartData = @json($chartData);

  // Define colors
  const colors = [
    'rgba(105, 108, 255, 0.8)',
    'rgba(3, 195, 236, 0.8)',
    'rgba(113, 221, 55, 0.8)',
    'rgba(255, 159, 67, 0.8)',
    'rgba(255, 71, 87, 0.8)',
    'rgba(156, 39, 176, 0.8)',
    'rgba(255, 193, 7, 0.8)',
    'rgba(0, 150, 136, 0.8)',
  ];

  chartData.forEach((data, index) => {
    const ctx = document.getElementById('chart-' + data.id);
    if (ctx) {
      // Determine chart type based on data
      let chartType = 'bar';

      // Use pie chart for questions with fewer options
      if (data.labels.length <= 5) {
        chartType = 'pie';
      } else if (data.labels.length <= 8) {
        chartType = 'doughnut';
      }

      const chartConfig = {
        type: chartType,
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Responses',
            data: data.data,
            backgroundColor: colors,
            borderColor: '#fff',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              display: true,
              position: chartType === 'bar' ? 'top' : 'bottom'
            },
            title: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.label || '';
                  if (label) {
                    label += ': ';
                  }
                  label += context.parsed.y || context.parsed;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = ((context.parsed.y || context.parsed) / total * 100).toFixed(1);
                  label += ` (${percentage}%)`;
                  return label;
                }
              }
            }
          }
        }
      };

      // Add scales only for bar charts
      if (chartType === 'bar') {
        chartConfig.options.scales = {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        };
      }

      new Chart(ctx, chartConfig);
    }
  });

  // Add chart type switcher functionality
  document.querySelectorAll('.chart-type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const chartId = this.dataset.chartId;
      const chartType = this.dataset.chartType;
      const canvas = document.getElementById('chart-' + chartId);

      if (canvas) {
        // Destroy existing chart
        const existingChart = Chart.getChart(canvas);
        if (existingChart) {
          existingChart.destroy();
        }

        // Find the chart data
        const data = chartData.find(d => d.id == chartId);

        if (data) {
          const chartConfig = {
            type: chartType,
            data: {
              labels: data.labels,
              datasets: [{
                label: 'Responses',
                data: data.data,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 2
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: true,
              plugins: {
                legend: {
                  display: true,
                  position: chartType === 'bar' ? 'top' : 'bottom'
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      let label = context.label || '';
                      if (label) {
                        label += ': ';
                      }
                      label += context.parsed.y || context.parsed;
                      const total = context.dataset.data.reduce((a, b) => a + b, 0);
                      const percentage = ((context.parsed.y || context.parsed) / total * 100).toFixed(1);
                      label += ` (${percentage}%)`;
                      return label;
                    }
                  }
                }
              }
            }
          };

          if (chartType === 'bar') {
            chartConfig.options.scales = {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                }
              }
            };
          }

          new Chart(canvas, chartConfig);
        }
      }

      // Update active button state
      this.closest('.btn-group').querySelectorAll('.chart-type-btn').forEach(b => {
        b.classList.remove('active');
      });
      this.classList.add('active');
    });
  });
</script>
@endsection
