@extends('layouts.app')

@section('title', $projectModel->name . ' - Tasks')

@section('header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $projectModel->name }}</h1>
        <p class="text-gray-600 mt-2">Drag and drop tasks to reorder priorities</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
            ← Back to Projects
        </a>
        <button onclick="showCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
            Add New Task
        </button>
    </div>
</div>
@endsection

@section('content')

            <!-- Tasks List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                @if($tasks->count() > 0)
                    <div id="task-list" class="space-y-3">
                        @foreach($tasks as $task)
                            <div class="task-item bg-gray-50 border-2 border-gray-200 rounded-lg p-4 cursor-move hover:border-indigo-300 transition-all duration-200" data-id="{{ $task->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <!-- Drag Handle -->
                                        <div class="drag-handle text-gray-400 hover:text-indigo-500 cursor-move">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zM10 17a1 1 0 01-.707-.293l-3-3a1 1 0 011.414-1.414L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3A1 1 0 0110 17z"></path>
                                            </svg>
                                        </div>
                                        <div class="w-8 h-8 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-bold priority-badge">
                                            {{ $task->priority }}
                                        </div>
                                        <div class="flex-1">
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
@push('scripts')
    <script>
        // Initialize Sortable
        let sortable;
        if (document.getElementById('task-list')) {
            sortable = new Sortable(document.getElementById('task-list'), {
                animation: 300,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                handle: '.drag-handle, .task-item',
                onStart: function (evt) {
                    // Add visual feedback when dragging starts
                    document.body.classList.add('is-dragging');
                },
                onEnd: function (evt) {
                    // Remove visual feedback when dragging ends
                    document.body.classList.remove('is-dragging');
                    // Only update if the position actually changed
                    if (evt.oldIndex !== evt.newIndex) {
                        updateTaskOrder();
                    }
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

        function deleteTask(id, project_id) {
            if (confirm('Are you sure you want to delete this task?')) {
                // Show loading state
                const taskElement = document.querySelector(`[data-id="${id}"]`);
                if (taskElement) {
                    taskElement.style.opacity = '0.5';
                    taskElement.style.pointerEvents = 'none';
                }

                $.ajax({
                    url: `/projects/${project_id}/tasks/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            // Smooth removal animation
                            if (taskElement) {
                                taskElement.style.transform = 'translateX(-100%)';
                                setTimeout(() => {
                                    location.reload();
                                }, 300);
                            } else {
                                location.reload();
                            }
                        }
                    },
                    error: function() {
                        alert('Error deleting task. Please try again.');
                        // Restore element state
                        if (taskElement) {
                            taskElement.style.opacity = '1';
                            taskElement.style.pointerEvents = 'auto';
                        }
                    }
                });
            }
        }

        function updateTaskOrder() {
            const taskIds = [];
            const projectId = document.getElementById('project_id').value;

            // Show loading indicator
            const taskList = document.getElementById('task-list');
            taskList.style.pointerEvents = 'none';
            taskList.style.opacity = '0.7';

            document.querySelectorAll('.task-item').forEach((item, index) => {
                taskIds.push(item.getAttribute('data-id'));
                // Update priority badge immediately for visual feedback
                const priorityBadge = item.querySelector('.priority-badge');
                if (priorityBadge) {
                    priorityBadge.textContent = index + 1;
                }
            });

            $.ajax({
                url: `/projects/${projectId}/tasks/reorder`,
                method: 'POST',
                data: {
                    task_ids: taskIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Tasks reordered successfully');
                    // Show success feedback
                    showNotification('Tasks reordered successfully!', 'success');
                },
                error: function(xhr) {
                    console.error('Error reordering tasks:', xhr);
                    showNotification('Error reordering tasks. Page will reload.', 'error');
                    // Reload to revert changes on error
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                },
                complete: function() {
                    // Restore interaction
                    taskList.style.pointerEvents = 'auto';
                    taskList.style.opacity = '1';
                }
            });
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(full)';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
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
@endpush
@endsection

@push('styles')
<style>
    /* Enhanced Drag and Drop Styles */
    .sortable-ghost {
        opacity: 0.3;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transform: rotate(2deg) scale(0.98);
        border: 2px dashed #4f46e5;
    }

    .sortable-chosen {
        cursor: grabbing !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: scale(1.02);
        z-index: 999;
    }

    .sortable-drag {
        opacity: 1;
        transform: rotate(-1deg);
    }

    /* Enhanced drag handle */
    .drag-handle {
        transition: all 0.2s ease;
        cursor: grab;
    }

    .drag-handle:hover {
        color: #4f46e5;
        transform: translateX(2px);
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    /* Task item enhancements */
    .task-item {
        transition: all 0.2s ease;
        position: relative;
    }

    .task-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Body dragging state */
    body.is-dragging {
        cursor: grabbing;
        user-select: none;
    }

    body.is-dragging * {
        pointer-events: none;
    }

    body.is-dragging .task-item {
        pointer-events: auto;
    }

    /* Priority badge enhancement */
    .priority-badge {
        transition: all 0.2s ease;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .task-item:hover .priority-badge {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Button hover enhancements */
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    /* Notification animation */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@endpush
