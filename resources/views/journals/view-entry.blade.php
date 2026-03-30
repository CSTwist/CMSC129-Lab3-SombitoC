@extends('layouts.app')

@section('title', 'View Entry - The Journal')

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
        cursor: default;
    }
    .title-input:focus {
        outline: none;
        box-shadow: none;
        background: transparent;
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
        opacity: 0.4;
        pointer-events: none;
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
        cursor: default;
    }
    .content-input:focus {
        outline: none;
        box-shadow: none;
        background: transparent;
    }
</style>

<div class="create-entry-page">
    <div class="create-entry-card">

        <div class="create-header">
            <a href="javascript:history.back()" class="back-btn"><i class="bi bi-chevron-left"></i></a>

            <div class="date-display">{{ $journal->created_at->format('m/d/Y') }}</div>
        </div>

        <input type="text" class="form-control title-input" value="{{ $journal->title }}" readonly>

        <div class="d-flex align-items-center gap-3 mb-3">
            @if($journal->mood)
                <span class="badge rounded-pill px-3 py-2" style="background-color: var(--pink-btn); color: var(--navy-text); font-weight: 500; font-size: 0.95rem;">
                    Mood: {{ $journal->mood }}
                </span>
            @endif

            @if($journal->is_favorite)
                <span style="color: var(--navy-text); font-weight: 500; font-size: 0.95rem;">
                    <i class="bi bi-star-fill text-warning"></i> Favorited
                </span>
            @endif
        </div>

        <hr class="editor-divider">

        <textarea class="form-control content-input" readonly>{{ $journal->content }}</textarea>

    </div>
</div>
@endsection
