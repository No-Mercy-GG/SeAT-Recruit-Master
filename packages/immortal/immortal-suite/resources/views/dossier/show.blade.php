@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Applicant Dossier #{{ $application->id }}</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h4>Risk Score: {{ $risk['score'] }} ({{ ucfirst($risk['tier']) }})</h4>
            <p>Recommendation: {{ $risk['recommendation'] }}</p>
            <p>Confidence: {{ $risk['confidence'] }}</p>
            <p>Discord linked: {{ $application->discord_user_id ? 'Yes' : 'No' }}</p>
        </div>
    </div>

    <h3>Risk Findings</h3>
    <ul class="list-group">
        @foreach ($risk['findings'] as $finding)
            <li class="list-group-item">
                <strong>{{ $finding['summary'] }}</strong>
                <div>Severity: {{ $finding['severity'] }}</div>
                <div>Score: {{ $finding['score'] }}</div>
                <pre>{{ json_encode($finding['details'], JSON_PRETTY_PRINT) }}</pre>
            </li>
        @endforeach
    </ul>
</div>
@endsection
