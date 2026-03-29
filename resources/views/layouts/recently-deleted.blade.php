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

        @php $hasDeletedItems = true; @endphp

        @if(!$hasDeletedItems)
            <div class="empty-trash-container">
                <i class="bi bi-trash empty-trash-icon"></i>
                <h3 style="font-weight: 600; margin-bottom: 5px;">Nothing in the trash</h3>
                <p class="text-muted mb-4">Recently deleted entries will appear here</p>
                <a href="{{ route('dashboard') }}" class="btn btn-pink rounded-pill px-4">Back to my journal entries</a>
            </div>

        @else
            <div class="trash-warning-banner mb-4">
                <span style="font-weight: 500;">Entries that have been in Trash will be permanently deleted after 30 days.</span>
                <button class="btn btn-danger-custom" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">Empty Trash now</button>
            </div>

            <h4 class="month-divider">March 2026</h4>

            <div class="trash-card">
                <div class="trash-date-box">
                    <span class="trash-date-day">MON</span>
                    <span class="trash-date-num">16</span>
                </div>
                <div class="trash-content d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="trash-title">Lorem ipsum dolor sit amet</h5>
                        <p class="trash-snippet">Lorem ipsum dolor sit amet consectetur adipiscing elit. Sit amet consectetur adipiscing elit quisque faucibus ex. Adipiscing elit quisque faucibus ex sapien vitae pellentesque....</p>
                    </div>

                    <div class="dropdown ms-3">
                        <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 12px; background-color: var(--warning-bg);">
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"><i class="bi bi-arrow-counterclockwise"></i> Restore</a></li>
                            <li><hr class="dropdown-divider border-secondary opacity-25"></li>
                            <li><a class="dropdown-item text-danger d-flex align-items-center gap-2" href="#"><i class="bi bi-trash"></i> Delete Forever</a></li>
                        </ul>
                    </div>
                </div>
            </div>

        @endif
    </div>
</div>

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
