<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        .sortable-ghost {
            opacity: 0.4;
        }
        .task-item {
            transition: all 0.3s ease;
        }
        .task-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-800">{{$projectModel->name}}</h1>
                    <button onclick="showCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Add New Task
                    </button>
                </div>
                <p class="text-gray-600 mt-2">Drag and drop tasks to reorder priorities</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tasks List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                @if($tasks->count() > 0)
                    <div id="task-list" class="space-y-3">
                        @foreach($tasks as $task)
                            <div class="task-item bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-move" data-id="{{ $task->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                            {{ $task->priority }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $task->name }}</h3>
                                            <p class="text-sm text-gray-500">Created: {{ $task->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="editTask({{ $task->id }},'{{ $task->name }}','{{$projectModel->id}}')"
                                                class="text-blue-600 hover:text-blue-800 px-3 py-1 rounded transition duration-200">
                                            Edit
                                        </button>
                                        <button onclick="deleteTask('{{ $task->id}}','{{$projectModel->id}}')"
                                                class="text-red-600 hover:text-red-800 px-3 py-1 rounded transition duration-200">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">📋</div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No tasks yet!</h3>
                        <p class="text-gray-500 mb-4">Create your first task to get started.</p>
                        <button onclick="showCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200">
                            Add Your First Task
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create/Edit Task Modal -->
    <div id="taskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Add New Task</h2>
            <form id="taskForm">
                @csrf
                <div class="mb-4">
                    <label for="taskName" class="block text-sm font-medium text-gray-700 mb-2">Task Name</label>
                    <input type="text" hidden name="project_id" id="project_id" value="{{$projectModel->id}}">
                    <input type="text" id="taskName" name="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter task name" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideModal()"
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200">
                        Save Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Sortable
        let sortable;
        if (document.getElementById('task-list')) {
            sortable = new Sortable(document.getElementById('task-list'), {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    updateTaskOrder();
                }
            });
        }

        let editingTaskId = null;

        // CSRF Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function showCreateModal() {
            editingTaskId = null;
            document.getElementById('modalTitle').textContent = 'Add New Task';
            document.getElementById('taskName').value = '';
            document.getElementById('taskModal').classList.remove('hidden');
            document.getElementById('taskModal').classList.add('flex');
            document.getElementById('taskName').focus();
        }

            function editTask(id, name, project_id) {
            editingTaskId = id;
            // Remove the redundant line: project_id = project_id;
            document.getElementById('modalTitle').textContent = 'Edit Task';
            document.getElementById('taskName').value = name;
            document.getElementById('taskModal').classList.remove('hidden');
            document.getElementById('taskModal').classList.add('flex');
            document.getElementById('taskName').focus();
        }

        function hideModal() {
            document.getElementById('taskModal').classList.add('hidden');
            document.getElementById('taskModal').classList.remove('flex');
            editingTaskId = null;
        }

        function deleteTask(id,project_id) {
            if (confirm('Are you sure you want to delete this task?')) {
                $.ajax({

                    url: `/projects/${project_id}/tasks/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('Error deleting task. Please try again.');
                    }
                });
            }
        }

        function updateTaskOrder() {
    const taskIds = [];
    const projectId = document.getElementById('project_id').value; // Get project ID

    document.querySelectorAll('.task-item').forEach((item, index) => {
        taskIds.push(item.getAttribute('data-id'));
        const priorityBadge = item.querySelector('.w-8.h-8');
        if (priorityBadge) {
            priorityBadge.textContent = index + 1;
        }
    });

    $.ajax({
        url: `/projects/${projectId}/tasks/reorder`, // Fixed URL
        method: 'POST',
        data: {
            task_ids: taskIds
        },

                success: function(response) {
                    console.log('Tasks reordered successfully');
                },
                error: function() {
                    console.error('Error reordering tasks');
                    location.reload(); // Reload to revert changes
                }
            });
        }

        // Handle form submission
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const projectId = document.getElementById('project_id').value;

            if (editingTaskId) {
                alert(editingTaskId);
                // Handle PUT request for editing
                const putData = {
                    name: formData.get('name'),
                    project_id: formData.get('project_id'),
                    task_id: editingTaskId,
                    _token: formData.get('_token')
                };

                $.ajax({
                    url: `/projects/${projectId}/tasks/${editingTaskId}/update`,
                    type: 'PUT',
                    data: putData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        let message = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('\n');
                        }
                        alert(message);
                    }
                });
            } else {
                // Handle POST request for creating
                $.ajax({
                    url: `/projects/${projectId}/tasks`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        let message = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('\n');
                        }
                        alert(message);
                    }
                });
            }
        });

        // Close modal when clicking outside
        document.getElementById('taskModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideModal();
            }
        });
    </script>
</body>
</html>
