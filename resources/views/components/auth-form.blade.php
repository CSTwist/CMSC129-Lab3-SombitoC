@props(['type' => 'login'])

<div class='login-auth-page'>
    <div class='auth-form-container'>
        <div class="container-fluid auth-container vh-100">
            <div class="row h-100 m-0">
                <div class="col-md-6 bg-image d-none d-md-block p-0" style="background-image: url('{{ asset('images/background.jpg') }}');"> </div>
                <div class="col-12 col-md-6 auth-form-panel d-flex align-items-center justify-content-center">
                    <div class="auth-content-wrapper p-0 w-100">
                        <div class='auth-right d-flex align-items-center mb-4'>
                            <div class='logo me-3'>
                                <img src="{{ asset('images/logo.png') }}" alt="The Journal Logo" class="header-logo-img" />
                            </div>
                            <h1 class='auth-title-header m-0'>The Journal</h1>
                        </div>

                        {{-- Form posts to different routes based on the type --}}
                        <form method="POST" action="{{ $type === 'signup' ? route('signup') : route('login') }}" class="auth-form">
                            @csrf

                            <h2 class="auth-header mb-0">
                                {{ $type === 'login' ? 'Welcome back!' : 'Create an account' }}
                            </h2>
                            <p class="auth-subtitle mb-4">
                                {{ $type === 'login' ? "Let's get you signed in." : 'Start your journaling journey.' }}
                            </p>

                            {{-- General Session Error Alert --}}
                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif

                            {{-- Username Field (Signup Only) --}}
                            @if($type === 'signup')
                                <div class='form-group mb-2'>
                                    <input type='text' name='username' class="form-control @error('username') is-invalid @enderror" placeholder='Username' value="{{ old('username') }}" required autofocus />
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endif

                            {{-- Email Field --}}
                            <div class='form-group mb-2'>
                                <input type='email' name='email' class="form-control @error('email') is-invalid @enderror" placeholder='Email' value="{{ old('email') }}" required {{ $type === 'login' ? 'autofocus' : '' }} />
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- Password Field --}}
                            <div class='form-group mb-3'>
                                <input type='password' name='password' class="form-control @error('password') is-invalid @enderror" placeholder='Password' required />
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- Confirm Password Field (Signup Only) --}}
                            @if($type === 'signup')
                                <div class='form-group mb-3'>
                                    <input type='password' name='password_confirmation' class="form-control" placeholder='Confirm Password' required />
                                </div>
                            @endif

                            {{-- Forgot Password / Remember Me (Login Only) --}}
                            @if($type === 'login')
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label text-muted" for="remember">
                                            <small>Remember me</small>
                                        </label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="forgot-psd text-decoration-none text-muted">
                                            <small>Forgot password?</small>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            {{-- Submit Button --}}
                            <button type='submit' class="btn btn-primary w-100 mt-5 py-2 auth-btn">
                                {{ $type === 'login' ? 'Log in' : 'Sign up' }}
                            </button>

                            {{-- Toggle links between Login and Signup --}}
                            @if($type === 'signup')
                                <div class='text-center mt-4'>
                                    Already have an account? <a href="{{ route('login') }}" class='auth-link'>Log in</a>
                                </div>
                            @else
                                <div class='text-center mt-4'>
                                    Don't have an account? <a href="{{ route('signup') }}" class='auth-link'>Sign up</a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
