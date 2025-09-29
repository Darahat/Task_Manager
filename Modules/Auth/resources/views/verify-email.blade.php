@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<div class="text-center mb-8">
    <h2 class="text-3xl font-bold text-gray-900">Email Verification</h2>
    <p class="text-gray-600 mt-2">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
    </p>
</div>

@if ($status == 'verification-link-sent')
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        A new verification link has been sent to the email address you provided during registration.
    </div>
@endif

<div class="flex items-center justify-between">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg transition duration-200 font-medium">
            Resend Verification Email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="text-gray-600 hover:text-gray-800 underline transition duration-200">
            Log Out
        </button>
    </form>
</div>
@endsection
