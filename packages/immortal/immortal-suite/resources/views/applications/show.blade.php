@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Application #{{ $application->id }}</h1>
    <p>Status: {{ $application->status }}</p>
    <p>Ticket: {{ $application->ticket_id }}</p>
    <p>Discord: {{ $application->discord_user_id }}</p>

    <form method="POST" action="{{ route('immortal.applications.status', $application) }}">
        @csrf
        <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status">
                @foreach ([
                    'NEW',
                    'SCREENING',
                    'INTERVIEW',
                    'DIRECTOR_REVIEW',
                    'ACCEPTED',
                    'DENIED',
                    'COMPLETED'
                ] as $status)
                    <option value="{{ $status }}" @selected($application->status === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Deny Reason</label>
            <select class="form-control" name="deny_reason">
                <option value="">Select reason (if denied)</option>
                @foreach ($denyReasons as $reason)
                    <option value="{{ $reason }}">{{ $reason }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea class="form-control" name="notes"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Update Status</button>
    </form>

    <form method="POST" action="{{ route('immortal.applications.claim', $application) }}" class="mt-3">
        @csrf
        <button class="btn btn-secondary" type="submit">Claim Application</button>
    </form>

    <h3 class="mt-4">Notes</h3>
    <pre>{{ $application->notes }}</pre>

    <h3 class="mt-4">History</h3>
    <ul class="list-group">
        @foreach ($history as $entry)
            <li class="list-group-item">
                <strong>{{ $entry->action }}</strong>
                <div>Status: {{ $entry->status }}</div>
                <div>User: {{ $entry->user_id }}</div>
                <div>At: {{ $entry->created_at }}</div>
                @if ($entry->notes)
                    <pre>{{ $entry->notes }}</pre>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endsection
