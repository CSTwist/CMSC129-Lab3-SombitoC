@extends('layouts.app')

@section('title', 'Edit Entry - The Journal')

@section('content')
<style>
    .create-entry-page {
        background-color: var(--bg-color, #CFDCE3);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    .create-entry-card {
        background-color: var(--cream-card, #ECEBE4);
        border-radius: 20px;
        width: 100%;
        max-width: 900px;
        min-height: 80vh;
        padding: 2rem 3rem;
        position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
    }
    .create-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }
    .back-btn {
        color: var(--navy-text, #153B50);
        font-size: 1.5rem;
        cursor: pointer;
        text-decoration: none;
        font-weight: bold;
    }
    .date-display {
        flex-grow: 1;
        text-align: center;
        color: var(--navy-text, #153B50);
        font-size: 1.2rem;
        margin-right: 1.5rem;
    }
    .title-input {
        border: none;
        background: transparent;
        font-size: 2rem;
        font-weight: 600;
        color: var(--navy-text, #153B50);
        padding: 0;
        margin-bottom: 0.5rem;
    }
    .title-input:focus {
        outline: none;
        box-shadow: none;
        background: transparent;
    }
    .title-input::placeholder {
        color: var(--navy-text, #153B50);
    }
    .editor-divider {
        border-top: 1px solid #ccc;
        margin: 0 0 1rem 0;
    }
    .editor-toolbar {
        display: flex;
        gap: 15px;
        margin-bottom: 1.5rem;
        color: #333;
        font-size: 1.1rem;
    }
    .editor-toolbar i {
        cursor: pointer;
    }
    .editor-toolbar .divider-vertical {
        color: #ccc;
        margin: 0 -5px;
    }
    .content-input {
        border: none;
        background: transparent;
        width: 100%;
        flex-grow: 1;
        font-size: 1.1rem;
        resize: none;
        padding: 0;
        color: #333;
    }
    .content-input:focus {
        outline: none;
        box-shadow: none;
        background: transparent;
    }
    .content-input::placeholder {
        color: #555;
    }
    .save-btn-container {
        display: flex;
        justify-content: flex-end;
        margin-top: auto;
        padding-top: 1rem;
    }
    .btn-save {
        background-color: var(--lavender-btn, #DAB3F3);
        color: var(--navy-text, #153B50);
        font-weight: 600;
        border-radius: 20px;
        padding: 10px 30px;
        border: none;
        transition: opacity 0.2s;
    }
    .btn-save:hover {
        opacity: 0.8;
    }
</style>

<div class="create-entry-page">
    <form action="{{ route('journals/update', $journal->id) }}" method="POST" class="create-entry-card">
        @csrf
        @method('PUT')

        <div class="create-header">
            <a href="javascript:history.back()" class="back-btn"><i class="bi bi-chevron-left"></i></a>
            <div class="date-display">{{ $journal->created_at->format('m/d/Y') }}</div>
        </div>

        <input type="text" name="title" class="form-control title-input" placeholder="Enter title" value="{{ old('title', $journal->title) }}" required autofocus>

        <div class="d-flex align-items-center gap-4 mb-3">
            <select name="mood" class="form-select border-0 bg-transparent shadow-none p-0" style="color: var(--navy-text); font-weight: 500; font-size: 1rem; width: auto; cursor: pointer;">
                <option value="">+ Add Mood</option>
                <option value="Happy" {{ old('mood', $journal->mood) == 'Happy' ? 'selected' : '' }}>Happy</option>
                <option value="Sad" {{ old('mood', $journal->mood) == 'Sad' ? 'selected' : '' }}>Sad</option>
                <option value="Excited" {{ old('mood', $journal->mood) == 'Excited' ? 'selected' : '' }}>Excited</option>
                <option value="Calm" {{ old('mood', $journal->mood) == 'Calm' ? 'selected' : '' }}>Calm</option>
                <option value="Anxious" {{ old('mood', $journal->mood) == 'Anxious' ? 'selected' : '' }}>Anxious</option>
                <option value="Productive" {{ old('mood', $journal->mood) == 'Productive' ? 'selected' : '' }}>Productive</option>
            </select>

            <div class="form-check d-flex align-items-center gap-2 m-0">
                <input class="form-check-input mt-0" type="checkbox" name="is_favorite" value="1" id="favoriteCheck" {{ old('is_favorite', $journal->is_favorite) ? 'checked' : '' }} style="cursor: pointer;">
                <label class="form-check-label" for="favoriteCheck" style="color: var(--navy-text); font-weight: 500; font-size: 1rem; cursor: pointer;">
                    <i class="bi bi-star-fill text-warning"></i> Favorite
                </label>
            </div>
        </div>

        <hr class="editor-divider">

        <div class="editor-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none toolbar-btn" data-format="bold" title="Bold (Ctrl+B)">
                    <i class="bi bi-type-bold"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none toolbar-btn" data-format="italic" title="Italic (Ctrl+I)">
                    <i class="bi bi-type-italic"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none toolbar-btn" data-format="heading" title="Heading">
                    <i class="bi bi-type-h2"></i>
                </button>
                <span class="divider-vertical">|</span>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none toolbar-btn" data-format="list" title="Bullet List">
                    <i class="bi bi-list-ul"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none toolbar-btn" data-format="quote" title="Quote">
                    <i class="bi bi-quote"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none toolbar-btn" data-format="code" title="Code">
                    <i class="bi bi-code-slash"></i>
                </button>
            </div>
            <div id="word-stats" class="text-muted" style="font-size: 0.85rem;">
                <span id="word-count">0</span> words &bull; <span id="reading-time">1</span> min read
            </div>
        </div>

        <textarea name="content" id="journal-content" class="form-control content-input" placeholder="Start writing here..." required>{{ old('content', $journal->content) }}</textarea>

        <div class="save-btn-container">
            <button type="submit" class="btn-save shadow-sm">Save entry</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('journal-content');
    const wordCountEl = document.getElementById('word-count');
    const readingTimeEl = document.getElementById('reading-time');
    const toolbarButtons = document.querySelectorAll('.toolbar-btn');

    function updateStats() {
        const text = textarea.value.trim();
        const words = text.length > 0 ? text.split(/\s+/).length : 0;
        const readTime = Math.max(1, Math.ceil(words / 200));
        wordCountEl.textContent = words;
        readingTimeEl.textContent = readTime;
    }

    textarea.addEventListener('input', updateStats);
    updateStats();

    function wrapSelection(prefix, suffix = prefix) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const selected = text.substring(start, end);
        const replacement = prefix + (selected || 'text') + suffix;
        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        textarea.focus();
        textarea.setSelectionRange(start + prefix.length, start + replacement.length - suffix.length);
        updateStats();
    }

    toolbarButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const format = btn.getAttribute('data-format');
            if (format === 'bold') wrapSelection('**', '**');
            else if (format === 'italic') wrapSelection('*', '*');
            else if (format === 'heading') wrapSelection('### ', '');
            else if (format === 'list') wrapSelection('- ', '');
            else if (format === 'quote') wrapSelection('> ', '');
            else if (format === 'code') wrapSelection('`', '`');
        });
    });

    textarea.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
            e.preventDefault();
            wrapSelection('**', '**');
        } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'i') {
            e.preventDefault();
            wrapSelection('*', '*');
        }
    });
});
</script>
@endsection
