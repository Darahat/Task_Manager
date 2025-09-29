<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Task Details</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('taskmanagement.edit', $task->id) }}"
                       class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200">
                        Edit
                    </a>
                    <a href="{{ route('taskmanagement.index') }}"
                       class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center text-lg font-bold">
                        {{ $task->priority }}
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $task->name }}</h2>
                        <p class="text-sm text-gray-600">Priority: {{ $task->priority }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                    <div>
                        <h3 class="font-semibold text-gray-700">Created</h3>
                        <p class="text-gray-600">{{ $task->created_at->format('M d, Y') }}</p>
                        <p class="text-sm text-gray-500">{{ $task->created_at->format('H:i:s') }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-700">Last Updated</h3>
                        <p class="text-gray-600">{{ $task->updated_at->format('M d, Y') }}</p>
                        <p class="text-sm text-gray-500">{{ $task->updated_at->format('H:i:s') }}</p>
                    </div>
                </div>

                @if($task->created_at != $task->updated_at)
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> This task was last modified {{ $task->updated_at->diffForHumans() }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Delete Form -->
            <div class="mt-8 pt-6 border-t">
                <form action="{{ route('taskmanagement.destroy', $task->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-200">
                        Delete Task
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
