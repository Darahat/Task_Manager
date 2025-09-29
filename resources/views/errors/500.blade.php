<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - Task Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-100 min-h-screen flex items-center justify-center">
    <div class="max-w-lg mx-auto text-center px-6">
        <!-- Error Icon -->
        <div class="mb-8">
            <div class="pulse">
                <svg class="mx-auto w-24 h-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                Something Went Wrong
            </h1>
            <p class="text-lg text-gray-600 mb-6">
                We're experiencing some technical difficulties. Our team has been notified and is working to fix the issue.
            </p>
        </div>

        <!-- Error Details -->
        @if(isset($exception) && config('app.debug'))
            <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg text-left">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Error Details (Debug Mode)</h3>
                <p class="text-sm text-red-700">{{ $exception->getMessage() }}</p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="space-y-4 md:space-y-0 md:space-x-4 md:flex md:justify-center">
            <button onclick="location.reload()"
                    class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Try Again
            </button>

            <a href="{{ route('projects.dashboard') }}"
               class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                </svg>
                Go to Dashboard
            </a>
        </div>

        <!-- Support Information -->
        <div class="mt-12 p-6 bg-white bg-opacity-50 rounded-lg border border-orange-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Need Help?</h3>
            <p class="text-sm text-gray-600 mb-4">
                If this problem persists, please try the following:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                    </svg>
                    <span>Clear your browser cache</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                    </svg>
                    <span>Check your internet connection</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                    </svg>
                    <span>Try a different browser</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                    </svg>
                    <span>Wait a few minutes</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Task Management System • Error Code: {{ $__env->yieldContent('code', '500') }}
            </p>
        </div>
    </div>
</body>
</html>
