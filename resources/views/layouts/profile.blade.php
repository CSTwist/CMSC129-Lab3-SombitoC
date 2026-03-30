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
                <button class="btn btn-purple" id="editProfileBtn">Edit your profile</button>
            </div>

            <div class="mb-4 w-75">
                <label class="profile-label d-block">Username</label>
                <input type="text" class="form-control profile-input w-75" value="{{ auth()->user()->name }}" disabled>
            </div>

            <div class="mb-4 w-75">
                <label class="profile-label d-block">Email</label>
                <input type="email" class="form-control profile-input w-75" value="{{ auth()->user()->email }}" disabled>
            </div>

            <div class="mb-5">
                <label class="profile-label d-block">Password</label>
                <button class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#passwordModal">Change your password</button>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5">
                <button class="btn btn-gray">Cancel</button>
                <button class="btn btn-purple">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
