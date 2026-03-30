<div class="journal-entry-card d-flex position-relative">

    {{-- Left Date Box --}}
    <div class="journal-date-box d-flex flex-column align-items-center justify-content-center">
        <span class="journal-day">{{ strtoupper($journal->created_at->format('D')) }}</span>
        <span class="journal-date">{{ $journal->created_at->format('d') }}</span>
    </div>

    {{-- Right Content Box --}}
    <div class="journal-content-box p-4 flex-grow-1 position-relative">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="journal-title mb-0">{{ $journal->title }}</h5>

            {{-- Three-dot Dropdown Menu --}}
            <div class="dropdown ms-3">
                <button class="btn btn-link text-dark p-0 text-decoration-none shadow-none dropdown-toggle-kebab" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical" style="font-size: 1.2rem; color: var(--navy-text);"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end journal-dropdown-menu shadow-sm">
                    <li>
                        {{-- Changed from triggering a modal to a standard link pointing to the new edit page --}}
                        <a href="{{ route('journals/edit', $journal->id) }}" class="dropdown-item journal-dropdown-item d-flex align-items-center gap-2 text-decoration-none" style="color: var(--navy-text);">
                            <i class="bi bi-pencil" style="font-size: 0.9rem;"></i> Edit
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('journals/delete', $journal->id) }}" method="POST" class="m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item journal-dropdown-item text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-trash" style="font-size: 0.9rem;"></i> Delete
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <p class="journal-snippet mb-0">
            {{ \Illuminate\Support\Str::limit(strip_tags($journal->content), 150) }}
        </p>
    </div>
</div>
