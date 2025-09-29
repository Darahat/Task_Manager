@extends('layouts.auth')

@section('title', 'Login')
@section('subtitle', 'Sign in to your account')

@section('content')
<form method="POST" action="{{ route('auth.login.store') }}" class="space-y-6">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
        <div class="mt-1">
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}" required autofocus
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

    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox"
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="remember" class="ml-2 block text-sm text-gray-900">
                Remember me
            </label>
        </div>

        <div class="text-sm">
            <a href="{{ route('auth.password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                Forgot your password?
            </a>
        </div>
    </div>

    <div>
        <button type="submit"
                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd" />
                </svg>
            </span>
            Sign in
        </button>
    </div>
</form>
@endsection

@section('footer-links')
<a href="{{ route('auth.register') }}" class="text-indigo-200 hover:text-white transition duration-150 ease-in-out">
    Don't have an account? Sign up
</a>
@endsection
