@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Immortal Suite Dashboard</h1>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5>Total Applications</h5>
                    <p>{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5>New</h5>
                    <p>{{ $stats['new'] }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5>Screening</h5>
                    <p>{{ $stats['screening'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
