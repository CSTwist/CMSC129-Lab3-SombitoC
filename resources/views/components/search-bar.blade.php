{{-- Updated action to url()->current() so it works dynamically on both Dashboard AND Trash page! --}}
<form action="{{ url()->current() }}" method="GET" class="search-bar d-flex align-items-center m-0">

    {{-- Preserve active filters when searching --}}
    @if(request('month'))
        <input type="hidden" name="month" value="{{ request('month') }}">
    @endif
    @if(request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif

    <div class="input-group search-input m-0">
        <span class="input-group-text search-icon-wrapper border-end-0">
            <i class="bi bi-search search-icon"></i>
        </span>

        <input
            type="text"
            name="search"
            class="form-control search-input-field border-start-0"
            placeholder="Search entries..."
            value="{{ request('search') }}"
        />
    </div>
</form>
