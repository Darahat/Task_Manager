@extends('layouts.auth')

@section('title', 'Confirm Password')

@section('content')
<div class="text-center mb-8">
    <h2 class="text-3xl font-bold text-gray-900">Confirm Password</h2>
    <p class="text-gray-600 mt-2">
        This is a secure area of the application. Please confirm your password before continuing.
    </p>
</div>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <!-- Password -->
    <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
        <input id="password"
               type="password"
               name="password"
               required
               autocomplete="current-password"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
        @error('password')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg transition duration-200 font-medium">
        Confirm
    </button>
</form>
@endsection
