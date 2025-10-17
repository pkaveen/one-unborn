@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container py-4">
    <h3 class="fw-bold text-primary mb-3">Edit Email Template</h3>

    <div class="card shadow border-0 p-4">
        <form action="{{ route('emails.update', $template->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Company Select --}}
            <div class="mb-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-control" required>
                    <option value="">-- Select Company --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ $template->company_id == $company->id ? 'selected' : '' }}>
                            {{ $company->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Subject --}}
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" 
                       value="{{ old('subject', $template->subject) }}" required>
                @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            {{-- Body --}}
            <div class="mb-3">
                <label class="form-label">Body</label>
                <textarea name="body" class="form-control" rows="8" required>{{ old('body', $template->body) }}</textarea>
                <small class="text-muted">
                    You can use placeholders like <code>{{ '{name}' }}</code>, 
                    <code>{{ '{company}' }}</code>, 
                    <code>{{ '{email}' }}</code>
                    <code>{{ '{joining_date}' }}</code>.
                </small>
                @error('body') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            {{-- Buttons --}}
            <button type="submit" class="btn btn-primary">Update Template</button>
            <a href="{{ route('emails.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
