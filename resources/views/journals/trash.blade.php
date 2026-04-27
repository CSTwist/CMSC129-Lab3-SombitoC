@extends('layouts.app')

@section('title', 'Recently Deleted - The Journal')

@section('content')
<div class="d-flex vh-100 overflow-hidden">
    <div class="d-flex align-items-center h-100">
        <x-left-sidebar />
    </div>

    <div class="main-container flex-grow-1 p-5 position-relative d-flex flex-column h-100 overflow-hidden">

        {{-- SUCCESS TOAST NOTIFICATION --}}
        @if(session('success'))
            <div id="successToast" class="position-absolute d-flex align-items-center justify-content-center text-center shadow-sm" style="
                top: 40px; left: 50%; transform: translateX(-50%);
                background-color: #F1F4EA; color: #2F613A;
                border: 1px solid rgba(0,0,0,0.15); border-radius: 12px;
                padding: 25px 30px; font-size: 1.3rem; font-weight: 600;
                z-index: 1050; max-width: 380px; line-height: 1.4;
                transition: opacity 0.5s ease-in-out;
            ">
                {{ session('success') }}
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(() => {
                        const toast = document.getElementById('successToast');
                        if (toast) {
                            toast.style.opacity = '0';
                            setTimeout(() => toast.remove(), 500);
                        }
                    }, 3500);
                });
            </script>
        @endif

        <div class="dashboard-content d-flex flex-column flex-grow-1 w-100 overflow-hidden">

            <div class="d-flex flex-row justify-content-between align-items-center mb-4 w-100 flex-shrink-0">
                <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--navy-text); margin-bottom: 0;">Recently Deleted</h1>
                <x-search-bar />
            </div>

            @if(!$hasDeletedItems)
                <div class="empty-trash-container text-center mt-5 w-100">
                    <i class="bi bi-trash empty-trash-icon mb-3" style="font-size: 3rem; color: var(--navy-text);"></i>
                    <h3 style="font-weight: 600; margin-bottom: 5px; color: var(--navy-text);">Nothing in the trash</h3>
                    <p class="text-muted mb-4">Recently deleted entries will appear here</p>
                    <a href="{{ route('dashboard') }}" class="btn text-decoration-none" style="background-color: var(--pink-btn); color: var(--navy-text); border-radius: 20px; font-weight: 600; padding: 8px 24px; border: 1px solid rgba(0,0,0,0.1);">Back to my journal entries</a>
                </div>
            @else
                <div class="d-flex flex-column align-items-center justify-content-center mb-4 w-100 flex-shrink-0" style="background-color: #FDF6EE; border: 1px solid rgba(0,0,0,0.05); border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <span class="mb-2" style="color: var(--navy-text); font-weight: 500;">Entries that have been in Trash will be permanently deleted after 30 days.</span>
                    <button class="btn btn-sm border-0" data-bs-toggle="modal" data-bs-target="#emptyTrashModal" style="background-color: #D9736A; color: white; border-radius: 20px; font-weight: 500; padding: 6px 20px;">
                        Empty Trash now
                    </button>
                </div>

                <div class="journal-list flex-grow-1 w-100 overflow-y-auto pe-3">
                    @foreach($groupedJournals as $monthYear => $entries)
                        <h3 class="month-group-header" style="color: var(--navy-text); font-size: 1.4rem; font-weight: 500; margin-top: 1.5rem; margin-bottom: 1rem;">{{ $monthYear }}</h3>

                        @foreach($entries as $journal)
                            <div class="journal-entry-card d-flex position-relative w-100" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.01)'" onmouseout="this.style.transform='scale(1)'" onclick="window.location.href='{{ route('journals/show', $journal->id) }}'">

                                <div class="journal-date-box d-flex flex-column align-items-center justify-content-center" style="background-color: rgba(0,0,0,0.04); border-right: 1px solid rgba(0,0,0,0.04); border-top-left-radius: 16px; border-bottom-left-radius: 16px; min-width: 100px; padding: 15px;">
                                    <span class="journal-day" style="font-size: 0.9rem; font-weight: 600; color: var(--navy-text); letter-spacing: 0.5px;">{{ strtoupper($journal->deleted_at->format('D')) }}</span>
                                    <span class="journal-date" style="font-size: 2.5rem; font-weight: 400; color: var(--navy-text); line-height: 1;">{{ $journal->deleted_at->format('d') }}</span>
                                </div>

                                <div class="journal-content-box p-4 flex-grow-1 position-relative">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="journal-title mb-0" style="color: var(--navy-text); font-weight: 700; font-size: 1.25rem;">{{ $journal->title }}</h5>

                                        <div class="dropdown ms-3" onclick="event.stopPropagation();">
                                            <button class="btn btn-link text-dark p-0 text-decoration-none shadow-none dropdown-toggle-kebab" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical" style="font-size: 1.2rem; color: var(--navy-text);"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end journal-dropdown-menu shadow-sm">
                                                <li>
                                                    <form action="{{ route('journals/restore', $journal->id) }}" method="POST" class="m-0 p-0">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item journal-dropdown-item d-flex align-items-center gap-2" style="color: var(--navy-text);">
                                                            <i class="bi bi-arrow-90deg-left" style="font-size: 0.9rem;"></i> Restore
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item journal-dropdown-item text-danger d-flex align-items-center gap-2 w-100 text-start border-0 bg-transparent" onclick="openDeleteModal({{ $journal->id }})">
                                                        <i class="bi bi-trash" style="font-size: 0.9rem;"></i> Delete Forever
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <p class="journal-snippet mb-0" style="font-size: 0.95rem; color: #4a5568; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; padding-right: 20px;">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($journal->content), 150) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @endforeach

                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-center mt-4 mb-4">
                        {{ $journals->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Empty Trash Modal --}}
<div class="modal fade" id="emptyTrashModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="background-color: #FDF6EE; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="text-center mb-4">
                <h4 class="mb-3" style="color: #B91C1C; font-weight: 600; font-size: 1.3rem;">Permanently delete all entries?</h4>
                <p class="small text-dark mb-1" style="font-weight: 500;">Are you sure you want to permanently</p>
                <p class="small text-dark mb-1" style="font-weight: 500;">delete all items in the trash?</p>
                <p class="small text-dark mb-0" style="font-weight: 500;">This action cannot be undone.</p>
            </div>
            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn" style="background-color: #D5D5D5; color: #333; border-radius: 20px; font-weight: 600; padding: 8px 24px;" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('trash/empty') }}" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background-color: #C84B31; color: white; border-radius: 20px; font-weight: 600; padding: 8px 24px;">Delete all entries</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Single Delete Modal --}}
<div class="modal fade" id="singleDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="background-color: #FDF6EE; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="text-center mb-4">
                <h4 class="mb-3" style="color: #B91C1C; font-weight: 600; font-size: 1.3rem;">Permanently delete this entry?</h4>
                <p class="small text-dark mb-1" style="font-weight: 500;">Are you sure you want to permanently</p>
                <p class="small text-dark mb-1" style="font-weight: 500;">delete this journal entry? This action</p>
                <p class="small text-dark mb-0" style="font-weight: 500;">cannot be undone.</p>
            </div>
            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn" style="background-color: #D5D5D5; color: #333; border-radius: 20px; font-weight: 600; padding: 8px 24px;" data-bs-dismiss="modal">Cancel</button>
                <form id="singleDeleteForm" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background-color: #C84B31; color: white; border-radius: 20px; font-weight: 600; padding: 8px 24px;">Delete entry</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id) {
        document.getElementById('singleDeleteForm').action = `/journals/${id}/force`;
        let modal = new bootstrap.Modal(document.getElementById('singleDeleteModal'));
        modal.show();
    }
</script>
@endsection
