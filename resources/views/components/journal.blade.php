<div class="journal-entry-card d-flex position-relative w-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'" onclick="window.location.href='{{ route('journals/show', $journal->id) }}'">

    <div class="journal-date-box d-flex flex-column align-items-center justify-content-center">
        <span class="journal-day">{{ strtoupper($journal->created_at->format('D')) }}</span>
        <span class="journal-date">{{ $journal->created_at->format('d') }}</span>
    </div>

    <div class="journal-content-box p-4 flex-grow-1 position-relative">
        <div class="d-flex justify-content-between align-items-start mb-2">

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="journal-title mb-0">{{ $journal->title }}</h5>

                <button type="button" class="btn btn-sm p-0 border-0 shadow-none favorite-toggle-btn" data-id="{{ $journal->id }}" onclick="event.stopPropagation(); toggleJournalFavorite(this, {{ $journal->id }})" title="{{ $journal->is_favorite ? 'Favorited' : 'Add to favorites' }}">
                    <i class="bi {{ $journal->is_favorite ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}" style="font-size: 1.05rem;"></i>
                </button>

                @if($journal->mood)
                    <span class="badge rounded-pill" style="background-color: var(--lavender-btn); color: var(--navy-text); font-weight: 500; font-size: 0.75rem;">
                        {{ $journal->mood_emoji }} {{ $journal->mood }}
                    </span>
                @endif

                <span class="text-muted small" style="font-size: 0.75rem;">
                    &bull; {{ $journal->reading_time }} min read
                </span>
            </div>

            <div class="dropdown ms-3" onclick="event.stopPropagation();">
                <button class="btn btn-link text-dark p-0 text-decoration-none shadow-none dropdown-toggle-kebab" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical" style="font-size: 1.2rem; color: var(--navy-text);"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end journal-dropdown-menu shadow-sm">
                    <li>
                        <a href="{{ route('journals/edit', $journal->id) }}" class="dropdown-item journal-dropdown-item d-flex align-items-center gap-2 text-decoration-none" style="color: var(--navy-text);">
                            <i class="bi bi-pencil" style="font-size: 0.9rem;"></i> Edit
                        </a>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item journal-dropdown-item text-danger d-flex align-items-center gap-2 w-100 text-start border-0 bg-transparent" onclick="openDashboardDeleteModal({{ $journal->id }})">
                            <i class="bi bi-trash" style="font-size: 0.9rem;"></i> Delete
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <p class="journal-snippet mb-0 text-muted" style="line-height: 1.5;">
            {{ \Illuminate\Support\Str::limit(strip_tags($journal->content), 160) }}
        </p>
    </div>
</div>

