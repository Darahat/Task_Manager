@extends('layouts.auth')

@section('title', 'Register')
@section('subtitle', 'Create your account')

@section('content')
<form method="POST" action="{{ route('auth.register.store') }}" class="space-y-6">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
        <div class="mt-1">
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}" required autofocus
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                   placeholder="Enter your full name">
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
        <div class="mt-1">
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}" required
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                   placeholder="Enter your email">
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <div class="mt-1">
            <input type="password" id="password" name="password" required
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                   placeholder="Enter your password">
        </div>
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
        <div class="mt-1">
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                   placeholder="Confirm your password">
        </div>
    </div>

    <div>
        <button type="submit"
                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                </svg>
            </span>
            Create account
        </button>
    </div>
</form>
@endsection

@section('footer-links')
<a href="{{ route('auth.login') }}" class="text-indigo-200 hover:text-white transition duration-150 ease-in-out">
    Already have an account? Sign in
</a>
@endsection
