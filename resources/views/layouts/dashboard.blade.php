@extends('layouts.app')

@section('title', 'Dashboard - The Journal')

@section('content')
    {{-- Changed min-vh-100 to vh-100 overflow-hidden to lock the page height --}}
    <div class="d-flex vh-100 overflow-hidden">
        {{-- Left Sidebar --}}
        <div class="d-flex align-items-center h-100">
            <x-left-sidebar />
        </div>

        {{-- Main Content Area - Added d-flex flex-column h-100 overflow-hidden --}}
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

            <div class="dashboard-content d-flex flex-column gap-4 flex-grow-1 w-100 overflow-hidden">

                {{-- Header, Filters, & Search - Added flex-shrink-0 so it doesn't compress --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center w-100 gap-3 flex-shrink-0">
                    <p id="dashboard-greetings" class="m-0">
                        Your Journal
                    </p>

                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <form id="filter-form" class="d-flex gap-2 m-0" onsubmit="event.preventDefault();">
                            <select id="monthFilter" name="month" class="form-select filter-select">
                                <option value="">All Months</option>
                                @foreach($availableMonths as $month)
                                    <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>

                            <select id="sortFilter" name="sort" class="form-select filter-select">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            </select>
                        </form>

                        <x-search-bar />
                    </div>
                </div>

                {{-- Grouped Journal List Container - Added overflow-y-auto and pe-2 (padding-right) for the scrollbar --}}
                <div id="journal-list-container" class="journal-list flex-grow-1 w-100 overflow-y-auto pe-3" style="transition: opacity 0.3s ease;">
                    @include('components/journal-list')
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="dashboardDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="background-color: #FDF6EE; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="text-center mb-4">
                    <h4 class="mb-3" style="color: #B91C1C; font-weight: 600; font-size: 1.3rem;">Delete this entry?</h4>
                    <p class="small text-dark mb-1" style="font-weight: 500;">Are you sure you want to permanently</p>
                    <p class="small text-dark mb-1" style="font-weight: 500;">delete this journal entry? This action</p>
                    <p class="small text-dark mb-0" style="font-weight: 500;">cannot be undone.</p>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn" style="background-color: #D5D5D5; color: #333; border-radius: 20px; font-weight: 600; padding: 8px 24px;" data-bs-dismiss="modal">Cancel</button>
                    <form id="dashboardDeleteForm" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn" style="background-color: #C84B31; color: white; border-radius: 20px; font-weight: 600; padding: 8px 24px;">Delete entry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- AJAX Dynamic Filtering Script --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const monthSelect = document.getElementById('monthFilter');
            const sortSelect = document.getElementById('sortFilter');
            const searchInput = document.querySelector('.search-input-field');
            const searchForm = searchInput ? searchInput.closest('form') : null;
            const container = document.getElementById('journal-list-container');

            function fetchFilteredJournals() {
                container.style.opacity = '0.4';

                const url = new URL('{{ route('dashboard') }}');
                if (monthSelect.value) url.searchParams.append('month', monthSelect.value);
                if (sortSelect.value) url.searchParams.append('sort', sortSelect.value);
                if (searchInput && searchInput.value) url.searchParams.append('search', searchInput.value);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error('Error fetching filtered journals:', error);
                    container.style.opacity = '1';
                });
            }

            if (monthSelect) monthSelect.addEventListener('change', fetchFilteredJournals);
            if (sortSelect) sortSelect.addEventListener('change', fetchFilteredJournals);

            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetchFilteredJournals();
                });

                let timeout = null;
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(fetchFilteredJournals, 400);
                });
            }
        });

        function openDashboardDeleteModal(id) {
            document.getElementById('dashboardDeleteForm').action = `/journals/${id}`;
            let modal = new bootstrap.Modal(document.getElementById('dashboardDeleteModal'));
            modal.show();
        }
    </script>
@endsection
