@extends('layouts.app')

@section('content')
<div class="py-3 px-8 flex justify-end">
    <button>
        <a href="{{ route('login') }}" class="bg-[#2A3BB7] px-6 py-3 rounded-full font-bold text-sm hover:bg-[#1A2793] text-white">Login</a>
    </button>
</div>
<div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 scale-[0.65]">
    <div class="">
        <div class="">
            <div class="" style="inline-size: fit-content">
                <div class="card shadow rounded-3xl px-32 py-20 flex flex-col justify-center">
                    <div class="text-center flex flex-col gap-3">
                        <h2 class="text-5xl text-[#3D53DB]">Buat Akun Baru</h2>
                        <p>Sudah Punya Akun ? <a class="underline-offset-1" href="{{route('login')}}"><u>Login</u></a></p>
                    </div>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3 w-[419px]">
                            <label for="name" class=" col-form-label text-md-end">{{ __('Nama') }}</label>

                            <div class="">
                                <input id="name" type="text" class="py-3 rounded-xl form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nama">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 w-[419px]">
                            <label for="email" class=" col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="">
                                <input id="email" type="email" class="py-3 rounded-xl form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 w-[419px]">
                            <label for="asal_sekolah" class=" col-form-label text-md-end">{{ __('Asal Sekolah') }}</label>

                            <div class="">
                                <input id="asal_sekolah" type="text" class="py-3 rounded-xl form-control @error('asal_sekolah') is-invalid @enderror" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required placeholder="Asal Sekolah">

                                @error('asal_sekolah')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 w-[419px]">
                            <label for="password" class=" col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="">
                                <input id="password" type="password" class="py-3 rounded-xl form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 w-[419px]">
                            <label for="password-confirm" class=" col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="">
                                <input id="password-confirm" type="password" class="py-3 rounded-xl form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password">
                            </div>
                        </div>

                        <div class="w-[419px]">
                            <div class=" ">
                                <button type="submit" class="btn btn-primary w-full bg-[#546FFF] font-bold py-3">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
