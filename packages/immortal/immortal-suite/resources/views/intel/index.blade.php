@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Intel Feed</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h5>Availability</h5>
            <p>Contacts: {{ $availability['contacts']['available'] ? 'Available' : 'Unavailable' }}</p>
            <p>Home Space: {{ $availability['home_space']['available'] ? 'Available' : 'Unavailable' }}</p>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>When</th>
                <th>Title</th>
                <th>Severity</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $event)
                <tr>
                    <td>{{ $event->created_at }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->severity }}</td>
                    <td><pre>{{ json_encode($event->details, JSON_PRETTY_PRINT) }}</pre></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No intel events recorded.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
