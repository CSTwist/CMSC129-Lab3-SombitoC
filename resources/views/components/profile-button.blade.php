<style>
    /* Hide the dropdown caret */
    .profile-dropdown-toggle::after {
        display: none !important;
    }
</style>

<div class="dropdown">
    {{-- The Toggle Button --}}
    <button
        class="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center profile-dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        style="width: 40px; height: 40px;"
    >
        👤
    </button>

    {{-- The Dropdown Menu --}}
    <ul class="dropdown-menu dropdown-menu-end shadow-sm">

        {{-- Standard Links --}}
        <li>
            <a class="dropdown-item" href="{{ url('/profile-settings') }}">Profile Settings</a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ url('/trash') }}">Trash</a>
        </li>

        <li><hr class="dropdown-divider"></li>

        {{-- Logout Form --}}
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger w-100 text-start">
                    Logout
                </button>
            </form>
        </li>
    </ul>
</div>
