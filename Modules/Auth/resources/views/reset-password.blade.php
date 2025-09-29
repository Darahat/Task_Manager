@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="text-center mb-8">
    <h2 class="text-3xl font-bold text-gray-900">Reset Password</h2>
    <p class="text-gray-600 mt-2">Enter your new password below</p>
</div>

<form method="POST" action="{{ route('password.store') }}">
    @csrf

    <!-- Password Reset Token -->
    <input type="hidden" name="token" value="{{ $token }}">

    <!-- Email Address -->
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
        <input id="email"
               type="email"
               name="email"
               value="{{ old('email', $email) }}"
               required
               autofocus
               autocomplete="username"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
        @error('email')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
        <input id="password"
               type="password"
               name="password"
               required
               autocomplete="new-password"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
        @error('password')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
        <input id="password_confirmation"
               type="password"
               name="password_confirmation"
               required
               autocomplete="new-password"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
        @error('password_confirmation')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg transition duration-200 font-medium">
        Reset Password
    </button>
</form>
@endsection
