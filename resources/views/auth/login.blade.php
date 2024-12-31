@extends('layouts.app')

@section('content')
<div class="py-3 px-8 flex justify-end">
    <button>
        <a href="{{ route('register') }}" class="bg-[#2A3BB7] px-6 py-3 rounded-full font-bold text-sm hover:bg-[#1A2793] text-white">Create Account</a>
    </button>
</div>
<div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
    <div class="">
        <div class="col-md-6 " style="inline-size: fit-content">
            <div class="card shadow rounded-3xl px-32 py-20 flex flex-col justify-center">
                <div class="text-center flex flex-col gap-3">
                    <h2 class="text-5xl text-[#3D53DB]">Login ke akun</h2>
                    <p>Belum punya akun ? <a class="underline-offset-1" href="{{route('register')}}"><u>Register</u></a></p>
                </div>
                <div class="card-body flex flex-col gap-3 justify-center items-center">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3 w-[419px]">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="py-3 rounded-xl form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email" autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 w-[419px]">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="py-3 rounded-xl form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div> --}}

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary w-full bg-[#546FFF] font-bold py-3">
                                {{ __('Login') }}
                            </button>
                            {{-- @if (Route::has('password.request'))
                                <a class="text-decoration-none" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif --}}
                        </div>
                    </form>
                </div>
                {{-- <div class="card-footer text-center">
                    <p class="mb-0">{{ __('Don\'t have an account?') }}
                        <a href="{{ route('register') }}" class="text-primary text-decoration-none">{{ __('Register here') }}</a>
                    </p>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection
