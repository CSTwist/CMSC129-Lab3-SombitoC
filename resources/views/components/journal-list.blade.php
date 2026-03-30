@if($isLoading)
    <div class="loading-entries text-center mt-5 text-muted">Loading entries...</div>
@elseif($groupedJournals->isEmpty())
    <div class="empty-entries text-center mt-5 text-muted">
        {{ $journals->isEmpty()
            ? 'No journal entries yet. Start writing!'
            : 'No journal entries match your filters.' }}
    </div>
@else
    @foreach($groupedJournals as $monthYear => $entries)
        <h3 class="month-group-header">{{ $monthYear }}</h3>

        @foreach($entries as $journal)
            <x-journal :journal="$journal" />
        @endforeach

    @endforeach
@endif
