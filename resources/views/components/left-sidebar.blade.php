<div class="left-sidebar d-flex flex-column p-4">
    <div class="logo-title-container d-flex align-items-center mb-5">
        <div class="logo me-3">
            <img src="{{ asset('images/logo.png') }}" alt="The Journal Logo" class="header-logo-img" />
        </div>
        <h1 class="m-0 sidebar-title" style="color: var(--navy-text); font-weight: 700;">App title</h1>
    </div>

    <nav class="nav flex-column gap-3">
        <a id="add-entry" class="d-flex align-items-center justify-content-center gap-2" href="{{ route('journals/create') }}">
            <i class="bi bi-plus"></i> New entry
        </a>

        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-journal-text"></i> Your Journal
        </a>
        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ url('/profile') }}">
            <i class="bi bi-person"></i> Profile
        </a>
        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('trash') ? 'active' : '' }}" href="{{ url('/recently-deleted') }}">
            <i class="bi bi-trash"></i> Recently Deleted
        </a>
    </nav>

    <div class="sign-out-container mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link w-100 d-flex align-items-center gap-2 border-0 text-start text-decoration-none">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</div>
