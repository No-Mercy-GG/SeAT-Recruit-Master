@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Immortal Suite Application</h1>
    <p>Ticket: {{ $application->ticket_id }}</p>
    @php
        $ready = collect($checklist)->every(fn ($item) => $item['complete'] || !empty($item['optional']));
    @endphp

    <h3>Checklist</h3>
    <ul>
        @foreach ($checklist as $item)
            <li>
                <strong>{{ $item['label'] }}</strong>
                - {{ $item['complete'] ? 'Complete' : 'Incomplete' }}
                @if (!empty($item['note']))
                    <em>{{ $item['note'] }}</em>
                @endif
            </li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('immortal.apply.done', $application) }}">
        @csrf
        <h3 class="mt-4">Alt Confirmation</h3>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="alts_confirmed" value="1" @checked($application->application_data['alts_confirmed'] ?? false)>
            <label class="form-check-label">I confirm all alts have been added.</label>
        </div>

        <h3 class="mt-4">Application Questions</h3>
        @foreach ($questions as $question)
            <div class="form-group">
                <label>{{ $question['label'] ?? 'Question' }}</label>
                <input class="form-control" name="application_data[{{ $question['id'] ?? '' }}]" value="{{ $application->application_data[$question['id'] ?? ''] ?? '' }}">
                @if (!empty($question['required']))
                    <small class="text-muted">Required</small>
                @endif
            </div>
        @endforeach
        <button class="btn btn-success" type="submit" @disabled(!$ready)>Done</button>
    </form>
</div>
@endsection
