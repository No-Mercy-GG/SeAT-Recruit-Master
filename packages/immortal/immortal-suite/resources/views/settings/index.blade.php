@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Immortal Suite Settings</h1>
    <form method="POST" action="{{ route('immortal.settings.update') }}">
        @csrf
        <h3>Feature Flags</h3>
        @foreach ($data['feature_flags'] as $key => $value)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="feature_flags[{{ $key }}]" value="1" @checked($value)>
                <label class="form-check-label">{{ $key }}</label>
            </div>
        @endforeach

        <h3 class="mt-4">Discord</h3>
        <div class="form-group">
            <label>Webhook URL</label>
            <input class="form-control" name="discord[webhook_url]" value="{{ $data['discord']['webhook_url'] ?? '' }}">
        </div>
        <div class="form-group">
            <label>Shared Secret</label>
            <input class="form-control" name="discord[shared_secret]" value="{{ $data['discord']['shared_secret'] ?? '' }}">
        </div>

        <h3 class="mt-4">Contacts Thresholds</h3>
        <div class="form-group">
            <label>Blue Minimum</label>
            <input class="form-control" name="contacts_thresholds[blue_min]" value="{{ $data['contacts_thresholds']['blue_min'] ?? '' }}">
        </div>
        <div class="form-group">
            <label>Hostile Maximum</label>
            <input class="form-control" name="contacts_thresholds[hostile_max]" value="{{ $data['contacts_thresholds']['hostile_max'] ?? '' }}">
        </div>

        <h3 class="mt-4">Intel Configuration (JSON)</h3>
        <div class="form-group">
            <label>Intel Config</label>
            <textarea class="form-control" name="intel_config" rows="6">{{ json_encode($data['intel'] ?? [], JSON_PRETTY_PRINT) }}</textarea>
        </div>

        <h3 class="mt-4">Alt Requirements</h3>
        <div class="form-group">
            <label>Mode</label>
            <select class="form-control" name="alts[mode]">
                @foreach (['manual' => 'Manual confirmation', 'account' => 'All characters on account', 'discord' => 'Discord linked characters'] as $key => $label)
                    <option value="{{ $key }}" @selected(($data['alts']['mode'] ?? 'manual') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="alts[require_confirmation]" value="1" @checked($data['alts']['require_confirmation'] ?? true)>
            <label class="form-check-label">Require manual confirmation when alts cannot be verified.</label>
        </div>

        <h3 class="mt-4">API Token</h3>
        <div class="form-group">
            <label>Token</label>
            <input class="form-control" name="api[token]" value="{{ $data['api']['token'] ?? '' }}">
        </div>
        <div class="form-group">
            <label>Admin Token</label>
            <input class="form-control" name="api[admin_token]" value="{{ $data['api']['admin_token'] ?? '' }}">
        </div>

        <h3 class="mt-4">Application Questions (JSON)</h3>
        <div class="form-group">
            <label>Questions</label>
            <textarea class="form-control" name="application_questions" rows="6">{{ json_encode($data['application_questions'] ?? [], JSON_PRETTY_PRINT) }}</textarea>
        </div>

        <h3 class="mt-4">Deny Reasons (JSON)</h3>
        <div class="form-group">
            <label>Deny Reasons</label>
            <textarea class="form-control" name="deny_reasons" rows="4">{{ json_encode($data['deny_reasons'] ?? [], JSON_PRETTY_PRINT) }}</textarea>
        </div>

        <button class="btn btn-primary mt-4" type="submit">Save Settings</button>
    </form>
</div>
@endsection
