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

        @if(!$hasDeletedItems)
            <div class="empty-trash-container">
                <i class="bi bi-trash empty-trash-icon"></i>
                <h3 style="font-weight: 600; margin-bottom: 5px;">Nothing in the trash</h3>
                <p class="text-muted mb-4">Recently deleted entries will appear here</p>
                <a href="{{ route('dashboard') }}" class="btn btn-pink rounded-pill px-4 py-2 text-decoration-none">Back to my journal entries</a>
            </div>
        @else
            <div class="trash-warning-banner mb-4">
                <span style="font-weight: 500;">Entries that have been in Trash will be permanently deleted after 30 days.</span>
                <button class="btn btn-danger-custom" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">Empty Trash now</button>
            </div>

            @foreach($trashedJournals as $journal)
                <div class="trash-card card w-75 mb-3">
                    <div class="d-flex w-100">
                        <div class="trash-date-box text-center">
                            <span class="trash-date-day">{{ $journal->deleted_at->format('M') }}</span>
                            <span class="trash-date-num">{{ $journal->deleted_at->format('d') }}</span>
                        </div>
                        <div class="trash-content d-flex flex-column justify-content-center">
                            <h5 class="trash-title">{{ $journal->title }}</h5>
                            <p class="trash-snippet mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($journal->content), 100) }}</p>

                            <div class="d-flex gap-2 mt-2">
                                <form action="{{ route('journals.restore', $journal->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-pink rounded-pill px-3">Restore</button>
                                </form>
                                <form action="{{ route('journals.forceDelete', $journal->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger-custom rounded-pill px-3">Delete Forever</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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
                <form action="{{ route('trash.empty') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-custom" style="background-color: #E07A5F;">Delete all entries</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
