@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Risk Engine</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Rule</th>
                <th>Enabled</th>
                <th>Weight</th>
                <th>Lookback Days</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rules as $rule)
                <tr>
                    <form method="POST" action="{{ route('immortal.risk.update', $rule) }}">
                        @csrf
                        <td>{{ $rule->name }}</td>
                        <td>
                            <input type="checkbox" name="enabled" value="1" @checked($rule->enabled)>
                        </td>
                        <td>
                            <input class="form-control" type="number" name="weight" value="{{ $rule->weight }}">
                        </td>
                        <td>
                            <input class="form-control" type="number" name="lookback_days" value="{{ $rule->lookback_days }}">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" type="submit">Save</button>
                        </td>
                    </form>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
