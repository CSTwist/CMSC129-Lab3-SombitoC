@extends('layouts.app')

@section('title', 'Dashboard - The Journal')

@section('content')
    <div class="d-flex min-vh-100">
        {{-- Left Sidebar --}}
        <div class="d-flex align-items-center">
            <x-left-sidebar />
        </div>
        
        {{-- Main Content Area --}}
        <div class="main-container flex-grow-1">

            <div class="dashboard-content d-flex flex-column p-4 gap-4 flex-grow-1">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <p id="dashboard-greetings">
                        Your Journal
                    </p>
                    <x-search-bar />
                </div>
                <div class="journal-list flex-grow-1">

                    {{-- Conditional Rendering for journal entries --}}
                    @if($isLoading)
                        <div class="loading-entries text-center">Loading entries...</div>
                    @elseif($filteredJournals->isEmpty())
                        <div class="empty-entries text-center">
                            {{ $journals->isEmpty()
                                ? 'No journal entries yet. Start writing!'
                                : 'No journal entries match your search.' }}
                        </div>
                    @else
                        @foreach($filteredJournals as $journal)
                            @include('partials.journal-entry', ['journal' => $journal])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal is still used for editing entries --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Journal Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" name="title" id="editTitle" class="form-control" placeholder="Entry Title">
                        </div>
                        <div class="mb-3">
                            <label>Content</label>
                            <textarea name="content" id="editContent" class="form-control" rows="6"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openEditModal(id, title, content) {
        document.getElementById('editTitle').value = title;
        document.getElementById('editContent').value = content;
        document.getElementById('editForm').action = `/journals/${id}`;
        let modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }
    </script>
@endsection
