
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">System Settings</h3>

    {{-- ✅ Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 p-4">
        <form action="{{ route('system.settings.update') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Timezone</label>
                    <input type="text" name="timezone" value="{{ $settings->timezone ?? '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Date Format</label>
                    <input type="text" name="date_format" value="{{ $settings->date_format ?? '' }}" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Language</label>
                    <input type="text" name="language" value="{{ $settings->language ?? '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ $settings->currency_symbol ?? '' }}" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Fiscal Year Start Month</label>
                    <input type="text" name="fiscal_start_month" value="{{ $settings->fiscal_start_month ?? '' }}" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
@endsection
