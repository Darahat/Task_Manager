<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Task</h1>

            <form action="{{ route('taskmanagement.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Task Name</label>
                    <input type="text" id="name" name="name"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @else border-gray-300 @enderror"
                           value="{{ old('name', $task->name) }}" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        <strong>Priority:</strong> {{ $task->priority }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Created:</strong> {{ $task->created_at->format('M d, Y H:i') }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Last Updated:</strong> {{ $task->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('taskmanagement.index') }}"
                       class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200">
                        Update Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
