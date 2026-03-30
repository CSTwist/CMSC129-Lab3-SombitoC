@extends('layouts.app')

@section('title', 'Dashboard - The Journal')

@section('content')
    <div class="d-flex min-vh-100">
        {{-- Left Sidebar --}}
        <div class="d-flex align-items-center">
            <x-left-sidebar />
        </div>

        {{-- Main Content Area --}}
        <div class="main-container flex-grow-1 p-5">

            <div class="dashboard-content d-flex flex-column gap-4 flex-grow-1">

                {{-- Header & Search --}}
                <div class="d-flex flex-row justify-content-between align-items-center" style="max-width: 850px;">
                    <p id="dashboard-greetings">
                        Your Journal
                    </p>
                    <x-search-bar />
                </div>

                {{-- Grouped Journal List --}}
                <div class="journal-list flex-grow-1">

                    @if($isLoading)
                        <div class="loading-entries text-center mt-5 text-muted">Loading entries...</div>
                    @elseif($groupedJournals->isEmpty())
                        <div class="empty-entries text-center mt-5 text-muted">
                            {{ $journals->isEmpty()
                                ? 'No journal entries yet. Start writing!'
                                : 'No journal entries match your search.' }}
                        </div>
                    @else
                        {{-- Loop through the Date groups (Month Year) --}}
                        @foreach($groupedJournals as $monthYear => $entries)
                            <h3 class="month-group-header">{{ $monthYear }}</h3>

                            {{-- Loop through the journals in each group --}}
                            @foreach($entries as $journal)
                                <x-journal :journal="$journal" />
                            @endforeach

                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Edit modal has been completely removed! It now loads the dedicated edit page. --}}
@endsection
