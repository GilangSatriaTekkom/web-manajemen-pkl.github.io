<!-- resources/views/dashboard.blade.php -->
<div class="flex flex-row">
    <div>
        @extends('layouts.app')
        @section('content')
        <div class="flex flex-row">
            @include('layouts.partials.sidebar')
            <div class="w-full ml-64">
                @include('layouts.partials.header')
                <div class="flex flex-row justify-between px-4">
                    <div class="pr-4 pl-2 py-2 border border-gray-300 rounded-md w-full max-w-[480px] flex flex-row items-center gap-3">
                        <input type="text" id="search-input" placeholder="Search..." class="border-none w-full">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_2207_4271)">
                            <path d="M9.58268 17.5C13.9549 17.5 17.4993 13.9556 17.4993 9.58335C17.4993 5.2111 13.9549 1.66669 9.58268 1.66669C5.21043 1.66669 1.66602 5.2111 1.66602 9.58335C1.66602 13.9556 5.21043 17.5 9.58268 17.5Z" stroke="#8E92BC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.3327 18.3333L16.666 16.6666" stroke="#8E92BC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_2207_4271">
                            <rect width="20" height="20" fill="white"/>
                            </clipPath>
                            </defs>
                            </svg>

                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-16 mx-[24px]">
                    @foreach ($users as $user)
                    <a href="{{ route('profile', ['id' => $user->id]) }}">
                        <div class="w-full flex flex-row items-center gap-2 my-7 shadow-sm">
                            @if ($user->profile_pict)
                                <img class="w-[48px] h-[48px] rounded-full" src="{{ asset('storage/' . $user->profile_pict) }}" alt="Profile Picture" class="img-fluid rounded-circle">
                            @else
                                <img class="w-[48px] h-[48px] rounded-full" src="{{ asset('images/default-profile.png') }}" alt="Profile Picture" class="img-fluid rounded-circle">
                            @endif
                            <div class="flex flex-col">
                                <h5 class="card-title">{{ $user->name }}</h5>
                                <p class="card-text">
                                    <strong>Email:</strong> {{ $user->email }}<br>
                                </p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endsection
    </div>

</div>
