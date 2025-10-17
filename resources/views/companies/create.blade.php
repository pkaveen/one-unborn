@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Company</h3>
    <div class="card shadow border-0 p-4">
        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('companies.partials.form')
            <div class="text-end">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
