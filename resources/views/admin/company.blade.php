@extends('layouts.app')
@section('title', 'Company Settings')

@section('content')
<div class="animate-fade-in">
    <div class="card" style="max-width:700px;">
        <div class="card-header">
            <h3>🏢 Company Settings</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.company.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $company->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-textarea" rows="3">{{ old('address', $company->address) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Company Logo</label>
                    @if($company->logo_path)
                        <div style="margin-bottom:0.75rem;">
                            <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Logo" style="max-height:60px;border-radius:var(--radius-sm);">
                        </div>
                    @endif
                    <input type="file" name="logo" class="form-input" accept="image/*">
                    <p class="form-hint">Upload a new logo (max 2MB)</p>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
