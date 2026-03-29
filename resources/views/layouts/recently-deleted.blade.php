@extends('layouts.app')

@section('title', 'Recently Deleted - The Journal')

@section('content')
<div class="d-flex min-vh-100">
    <div class="d-flex align-items-center">
        <x-left-sidebar />
    </div>

    <div class="main-container flex-grow-1 p-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-header-title">Recently Deleted</h1>
            <x-search-bar />
        </div>

        @php $hasDeletedItems = false; @endphp

        @if(!$hasDeletedItems)
            <div class="empty-trash-container">
                <i class="bi bi-trash empty-trash-icon"></i>
                <h3 style="font-weight: 600; margin-bottom: 5px;">Nothing in the trash</h3>
                <p class="text-muted mb-4">Recently deleted entries will appear here</p>
                <a href="{{ route('dashboard') }}" class="btn btn-pink rounded-pill px-4 py-2">Back to my journal entries</a>
            </div>

        @else
            {{-- Display list of recently deleted entries here --}}
            <div class="trash-warning-banner mb-4">
                <span style="font-weight: 500;">Entries that have been in Trash will be permanently deleted after 30 days.</span>
                <button class="btn btn-danger-custom" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">Empty Trash now</button>
            </div>

            @endif
    </div>
</div>

{{-- Empty Trash Modal --}}
<div class="modal fade" id="emptyTrashModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content profile-card" style="padding: 30px;">
            <div class="text-center mb-3">
                <h4 class="profile-label text-danger mb-3" style="font-size: 1.3rem;">Permanently delete all entries?</h4>
                <p class="small text-muted mb-1">Are you sure you want to permanently</p>
                <p class="small text-muted mb-1">delete all items in the trash?</p>
                <p class="small text-muted mb-4">This action cannot be undone.</p>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn btn-gray" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger-custom" style="background-color: #E07A5F;">Delete all entries</button>
            </div>
        </div>
    </div>
</div>
@endsection
