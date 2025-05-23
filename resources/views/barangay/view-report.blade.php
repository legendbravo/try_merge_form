@extends('layouts.barangay')

@section('title', 'View Report')
@section('page-title', 'View Report')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $report->reportType->name ?? 'Report' }}</h5>
            <small class="text-muted">Frequency: {{ ucfirst($report->frequency) }}</small>
        </div>
        <div class="card-body">
            <p><strong>Status:</strong> <span class="badge bg-info">{{ ucfirst($report->status) }}</span></p>
            <p><strong>Remarks:</strong> {{ $report->remarks ?? 'None' }}</p>
            <a href="{{ route('barangay.submissions') }}" class="btn btn-secondary mt-3">Back to My Submissions</a>
        </div>
    </div>
</div>
@endsection 