@extends('layouts.app')

@section('title', 'Profile - The Journal')

@section('content')
<div class="d-flex min-vh-100">
    <div class="d-flex align-items-center">
        <x-left-sidebar />
    </div>

    <div class="main-container flex-grow-1 p-5">
        <h1 class="page-header-title">Your Profile</h1>

        <div class="profile-card position-relative w-75">
            <div class="position-absolute top-0 end-0 p-4">
                <button type="button" class="btn btn-purple" id="editProfileBtn" onclick="enableEdit()">Edit your profile</button>
            </div>

            <form id="profileForm" method="POST" action="{{ route('profile/update') }}">
                @csrf
                @method('PATCH')

                <div class="mb-4 w-75">
                    <label class="profile-label d-block">Email</label>
                    <input type="email" name="email" id="emailInput" class="form-control profile-input w-75 @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" disabled>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="profile-label d-block">Password</label>
                    <button type="button" class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#passwordModal">Change your password</button>
                </div>

                <div id="profileActionButtons" class="d-none d-flex justify-content-end gap-3 mt-5">
                    <button type="button" class="btn btn-gray" onclick="cancelEdit()">Cancel</button>
                    <button type="submit" class="btn btn-purple">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Update Password Modal --}}
<div class="modal fade" id="passwordModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content profile-card" style="padding: 30px;">
            <div class="text-center mb-4">
                <h4 class="profile-label" style="font-size: 1.3rem;">Update Your Password</h4>
            </div>

            <form method="POST" action="{{ route('password/update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="small text-muted mb-1" style="font-weight: 500;">Current password</label>
                    <input type="password" name="current_password" class="form-control profile-input @error('current_password') is-invalid @enderror">
                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="small text-muted mb-1" style="font-weight: 500;">New password</label>
                    <input type="password" name="password" class="form-control profile-input @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="small text-muted mb-1" style="font-weight: 500;">Confirm new password</label>
                    <input type="password" name="password_confirmation" class="form-control profile-input">
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-gray" data-bs-dismiss="modal" onclick="closeFallbackModal('passwordModal')">Cancel</button>
                    <button type="submit" class="btn btn-pink">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Profile Updated Success Modal --}}
<div class="modal fade" id="profileUpdatedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content profile-card text-center" style="padding: 40px 30px;">
            <h4 class="mb-4" style="color: #2E7D32; font-weight: 700; font-size: 1.4rem;">Profile updated<br>successfully!</h4>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-purple" style="text-decoration: none;">Back to Your Journal page</a>
            </div>
        </div>
    </div>
</div>

{{-- Password Changed Success Modal --}}
<div class="modal fade" id="passwordUpdatedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content profile-card text-center" style="padding: 40px 30px;">
            <h4 class="mb-4" style="color: #2E7D32; font-weight: 700; font-size: 1.4rem;">Password changed<br>successfully!</h4>
            <div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-purple">Back to log in page</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Interactivity to edit email directly in the card
    function enableEdit() {
        const input = document.getElementById('emailInput');
        input.disabled = false;
        input.focus();

        // Show save/cancel, hide edit button
        document.getElementById('profileActionButtons').classList.remove('d-none');
        document.getElementById('editProfileBtn').classList.add('d-none');
    }

    function cancelEdit() {
        const input = document.getElementById('emailInput');
        input.disabled = true;
        input.value = "{{ auth()->user()->email }}"; // Reset to authenticated email

        // Hide save/cancel, show edit button
        document.getElementById('profileActionButtons').classList.add('d-none');
        document.getElementById('editProfileBtn').classList.remove('d-none');
    }

    function showFallbackModal(modalId) {
        const modal = document.getElementById(modalId);
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            new bootstrap.Modal(modal).show();
        } else {
            modal.classList.add('show');
            modal.style.display = 'block';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
        }
    }

    // Helper to close fallback modal
    function closeFallbackModal(modalId) {
        const modal = document.getElementById(modalId);
        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
    }

    // Automatically open validation error modal or success modals
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->has('current_password') || $errors->has('password'))
            showFallbackModal('passwordModal');
        @endif

        @if (session('status') === 'profile-updated')
            showFallbackModal('profileUpdatedModal');
        @endif

        @if (session('status') === 'password-updated')
            showFallbackModal('passwordUpdatedModal');
        @endif

        // If email validation failed, ensure the form stays in "edit mode"
        @if($errors->has('email'))
            enableEdit();
        @endif
    });
</script>
@endsection
