
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="text-primary mb-3">🏢 Company Settings</h3>

    {{-- ✅ Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('company.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Company Name *</label>
                <input type="text" name="company_name" class="form-control"
                       value="{{ old('company_name', $company->company_name ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Company Email</label>
                <input type="email" name="company_email" class="form-control"
                       value="{{ old('company_email', $company->company_email ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Contact Number</label>
                <input type="text" name="contact_no" class="form-control"
                       value="{{ old('contact_no', $company->contact_no ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Website</label>
                <input type="text" name="website" class="form-control"
                       value="{{ old('website', $company->website ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>GST Number</label>
                <input type="text" name="gst_number" class="form-control"
                       value="{{ old('gst_number', $company->gst_number ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Company Logo</label>
                <input type="file" name="company_logo" class="form-control">
                @if(!empty($company->company_logo))
                    <img src="{{ asset('storage/' . $company->company_logo) }}" class="mt-2" width="100">
                @endif
            </div>

            <div class="col-md-12 mb-3">
                <label>Address</label>
                <textarea name="address" class="form-control">{{ old('address', $company->address ?? '') }}</textarea>
            </div>
        </div>
        <!-- email -->
         <hr>
<h5 class="text-primary fw-bold mb-3">✉️ Email Settings</h5>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Mail Mailer</label>
        <input type="text" name="mail_mailer" class="form-control"
               value="{{ old('mail_mailer', $company->mail_mailer ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Mail Host</label>
        <input type="text" name="mail_host" class="form-control"
               value="{{ old('mail_host', $company->mail_host ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Mail Port</label>
        <input type="text" name="mail_port" class="form-control"
               value="{{ old('mail_port', $company->mail_port ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Mail Username</label>
        <input type="text" name="mail_username" class="form-control"
               value="{{ old('mail_username', $company->mail_username ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Mail Password</label>
        <input type="password" name="mail_password" class="form-control"
               value="{{ old('mail_password', $company->mail_password ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Mail Encryption</label>
        <input type="text" name="mail_encryption" class="form-control"
               value="{{ old('mail_encryption', $company->mail_encryption ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Mail From Address</label>
        <input type="email" name="mail_from_address" class="form-control"
               value="{{ old('mail_from_address', $company->mail_from_address ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Mail From Name</label>
        <input type="text" name="mail_from_name" class="form-control"
               value="{{ old('mail_from_name', $company->mail_from_name ?? '') }}">
    </div>
</div>

        <hr>
<h5 class="text-primary fw-bold mb-3">🌐 Social Media Links</h5>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="linkedin_url" class="form-label">LinkedIn URL</label>
        <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url', $company->linkedin_url) }}">
    </div>
    <div class="col-md-6">
        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
        <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $company->whatsapp_number) }}">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="facebook_url" class="form-label">Facebook URL</label>
        <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $company->facebook_url) }}">
    </div>
    <div class="col-md-6">
        <label for="instagram_url" class="form-label">Instagram URL</label>
        <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $company->instagram_url) }}">
    </div>
</div>
@if($company->linkedin_url)
    <a href="{{ $company->linkedin_url }}" target="_blank" class="me-2">
        <i class="bi bi-linkedin text-primary fs-4"></i>
    </a>
@endif
@if($company->whatsapp_number)
    <a href="https://wa.me/{{ $company->whatsapp_number }}" target="_blank" class="me-2">
        <i class="bi bi-whatsapp text-success fs-4"></i>
    </a>
@endif



        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
@endsection
