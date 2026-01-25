@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Applications</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Ticket</th>
                <th>Discord</th>
                <th>Assigned</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applications as $application)
                <tr>
                    <td>{{ $application->id }}</td>
                    <td>{{ $application->status }}</td>
                    <td>{{ $application->ticket_id }}</td>
                    <td>{{ $application->discord_user_id }}</td>
                    <td>{{ $application->assigned_to }}</td>
                    <td><a href="{{ route('immortal.applications.show', $application) }}">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $applications->links() }}
</div>
@endsection
