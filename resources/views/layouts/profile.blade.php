@extends('layouts.app')

@section('title', 'Profile - The Journal')

@section('content')
<div class="d-flex min-vh-100">
    <div class="d-flex align-items-center">
        <x-left-sidebar />
    </div>

    <div class="main-container flex-grow-1 p-5">
        <h1 class="page-header-title">Your Profile</h1>

        <form action="{{ route('profile.update') }}" method="POST" class="profile-card position-relative w-75">
            @csrf
            @method('PATCH')

            <div class="position-absolute top-0 end-0 p-4">
                <button type="button" class="btn btn-purple" id="editProfileBtn" onclick="enableEdit()">Edit your profile</button>
            </div>

            <form id="profileForm" method="POST" action="{{ route('profile/update') }}">
                @csrf
                @method('PATCH')

            <div class="mb-4 w-75">
                <label class="profile-label d-block">Email</label>
                <input type="email" class="form-control profile-input w-75" value="iska@up.edu.ph" disabled>
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

            <div class="d-flex justify-content-end gap-3 mt-5">
                <button class="btn btn-gray">Cancel</button>
                <button class="btn btn-purple">Save changes</button>
            </div>
        </form>
    </div>
</div>
