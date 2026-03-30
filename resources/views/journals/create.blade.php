@extends('layouts.app')

@section('title', 'New Entry - The Journal')

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
    <form action="{{ route('journals/store') }}" method="POST" class="create-entry-card">
        @csrf

        <div class="create-header">
            <a href="{{ route('dashboard') }}" class="back-btn"><i class="bi bi-chevron-left"></i></a>
            <div class="date-display">{{ date('m/d/Y') }}</div>
        </div>

        <input type="text" name="title" class="form-control title-input" placeholder="Enter title" required autofocus>

        <div class="d-flex align-items-center gap-4 mb-3">
            <select name="mood" class="form-select border-0 bg-transparent shadow-none p-0" style="color: var(--navy-text); font-weight: 500; font-size: 1rem; width: auto; cursor: pointer;">
                <option value="">+ Add Mood</option>
                <option value="Happy">Happy</option>
                <option value="Sad">Sad</option>
                <option value="Excited">Excited</option>
                <option value="Calm">Calm</option>
                <option value="Anxious">Anxious</option>
                <option value="Productive">Productive</option>
            </select>

            <div class="form-check d-flex align-items-center gap-2 m-0">
                <input class="form-check-input mt-0" type="checkbox" name="is_favorite" value="1" id="favoriteCheck" style="cursor: pointer;">
                <label class="form-check-label" for="favoriteCheck" style="color: var(--navy-text); font-weight: 500; font-size: 1rem; cursor: pointer;">
                    <i class="bi bi-star-fill text-warning"></i> Favorite
                </label>
            </div>
        </div>

        <hr class="editor-divider">

        <div class="editor-toolbar">
            <i class="bi bi-type-bold"></i>
            <span class="divider-vertical">|</span>
            <i class="bi bi-type-italic"></i>
            <span class="divider-vertical">|</span>
            <i class="bi bi-type-underline"></i>
            <span class="divider-vertical">|</span>
            <i class="bi bi-fonts"></i>
            <span class="divider-vertical">|</span>
            <i class="bi bi-text-left"></i>
            <span class="divider-vertical">|</span>
            <i class="bi bi-text-center"></i>
            <span class="divider-vertical">|</span>
            <i class="bi bi-text-right"></i>
            <span class="divider-vertical">|</span>
            <i class="bi bi-justify"></i>
            <span class="divider-vertical">|</span>
        </div>

        <textarea name="content" class="form-control content-input" placeholder="Start writing here..." required></textarea>

        <div class="save-btn-container">
            <button type="submit" class="btn-save">Save entry</button>
        </div>
    </form>
</div>
@endsection
