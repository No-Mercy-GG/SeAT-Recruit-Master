@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Discord Integration</h1>
    <p>Webhook URL: {{ $config['webhook_url'] ?? 'Not configured' }}</p>
    <p>Shared Secret: {{ $config['shared_secret'] ? 'Configured' : 'Not configured' }}</p>
</div>
@endsection
