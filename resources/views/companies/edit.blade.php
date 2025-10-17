@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Edit Company</h3>
    <div class="card shadow border-0 p-4">
        <form action="{{ route('companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('companies.partials.form', ['company' => $company])
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
