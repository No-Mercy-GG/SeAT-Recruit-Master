@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Audit Log</h1>
    <table class="table">
        <thead>
            <tr>
                <th>When</th>
                <th>User</th>
                <th>Action</th>
                <th>Context</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->created_at }}</td>
                    <td>{{ $log->user_id }}</td>
                    <td>{{ $log->action }}</td>
                    <td><pre>{{ json_encode($log->context, JSON_PRETTY_PRINT) }}</pre></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection
