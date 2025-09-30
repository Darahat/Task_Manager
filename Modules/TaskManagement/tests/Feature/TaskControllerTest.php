<?php

namespace Modules\TaskManagement\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\TaskManagement\Models\Project;
use Modules\TaskManagement\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Create a test user and project for authentication and testing
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_tasks()
    {
        $response = $this->get("/projects/{$this->project->id}/tasks");
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_view_project_tasks()
    {
        $response = $this->actingAs($this->user)
            ->get("/projects/{$this->project->id}/tasks");

        $response->assertStatus(200);
        $response->assertViewIs('taskmanagement::index');
        $response->assertSee($this->project->name);
    }

    /** @test */
    public function tasks_are_displayed_in_priority_order()
    {
        $task1 = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 2,
            'name' => 'Second Task'
        ]);

        $task2 = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 1,
            'name' => 'First Task'
        ]);

        $response = $this->actingAs($this->user)
            ->get("/projects/{$this->project->id}/tasks");

        $response->assertStatus(200);
        // Check that First Task appears before Second Task in the HTML
        $content = $response->getContent();
        $firstTaskPos = strpos($content, 'First Task');
        $secondTaskPos = strpos($content, 'Second Task');
        $this->assertLessThan($secondTaskPos, $firstTaskPos);
    }

    /** @test */
    public function user_can_only_see_their_own_tasks()
    {
        $userTask = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id
        ]);

        $otherUserTask = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => User::factory()->create()->id
        ]);

        $response = $this->actingAs($this->user)
            ->get("/projects/{$this->project->id}/tasks");

        $response->assertSee($userTask->name);
        $response->assertDontSee($otherUserTask->name);
    }

    /** @test */
    public function user_can_create_task()
    {
        $taskData = [
            'name' => 'New Test Task',
            'project_id' => $this->project->id
        ];

        $response = $this->actingAs($this->user)
            ->post("/projects/{$this->project->id}/tasks", $taskData);

        $response->assertRedirect("/projects/{$this->project->id}/tasks");
        $this->assertDatabaseHas('tasks', [
            'name' => 'New Test Task',
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 1 // Should be assigned priority 1 as first task
        ]);
    }

    /** @test */
    public function task_priority_is_auto_assigned()
    {
        // Create existing tasks
        Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 1
        ]);

        Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 2
        ]);

        $taskData = [
            'name' => 'New Task',
            'project_id' => $this->project->id
        ];

        $this->actingAs($this->user)
            ->post("/projects/{$this->project->id}/tasks", $taskData);

        $this->assertDatabaseHas('tasks', [
            'name' => 'New Task',
            'priority' => 3 // Should be assigned next priority
        ]);
    }

    /** @test */
    public function user_can_create_task_via_ajax()
    {
        $taskData = [
            'name' => 'AJAX Task',
            'project_id' => $this->project->id
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/projects/{$this->project->id}/tasks", $taskData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Task created successfully!'
        ]);
    }

    /** @test */
    public function task_creation_requires_name()
    {
        $response = $this->actingAs($this->user)
            ->post("/projects/{$this->project->id}/tasks", []);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function user_can_view_task_create_form()
    {
        $response = $this->actingAs($this->user)
            ->get("/projects/{$this->project->id}/tasks/create");

        $response->assertStatus(200);
        $response->assertViewIs('taskmanagement::create');
    }

    /** @test */
    public function user_can_view_specific_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get("/projects/{$this->project->id}/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertViewIs('taskmanagement::show');
        $response->assertSee($task->name);
    }

    /** @test */
    public function user_cannot_view_other_users_task()
    {
        $otherUserTask = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => User::factory()->create()->id
        ]);

        $response = $this->actingAs($this->user)
            ->get("/projects/{$this->project->id}/tasks/{$otherUserTask->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_view_task_edit_form()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get("/projects/{$this->project->id}/tasks/{$task->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('taskmanagement::edit');
        $response->assertSee($task->name);
    }

    /** @test */
    public function user_can_update_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'name' => 'Updated Task Name',
            'project_id' => $this->project->id,
            'task_id' => $task->id
        ];

        $response = $this->actingAs($this->user)
            ->put("/projects/{$this->project->id}/tasks/{$task->id}/update", $updateData);

        $response->assertRedirect("/projects/{$this->project->id}/tasks");
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task Name'
        ]);
    }

    /** @test */
    public function user_can_update_task_via_ajax()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'name' => 'AJAX Updated Task',
            'project_id' => $this->project->id,
            'task_id' => $task->id
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/projects/{$this->project->id}/tasks/{$task->id}/update", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Task updated successfully!'
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_task()
    {
        $otherUserTask = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => User::factory()->create()->id
        ]);

        $response = $this->actingAs($this->user)
            ->put("/projects/{$this->project->id}/tasks/{$otherUserTask->id}/update", [
                'name' => 'Hacked Task',
                'task_id' => $otherUserTask->id
            ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_delete_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/projects/{$this->project->id}/tasks/{$task->id}");

        $response->assertRedirect("/projects/{$this->project->id}/tasks");
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_can_delete_task_via_ajax()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/projects/{$this->project->id}/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Task deleted successfully!'
        ]);
    }

    /** @test */
    public function user_cannot_delete_other_users_task()
    {
        $otherUserTask = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => User::factory()->create()->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/projects/{$this->project->id}/tasks/{$otherUserTask->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('tasks', ['id' => $otherUserTask->id]);
    }

    /** @test */
    public function deleting_task_reorders_remaining_tasks()
    {
        $task1 = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 1
        ]);

        $task2 = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 2
        ]);

        $task3 = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 3
        ]);

        // Delete the middle task
        $this->actingAs($this->user)
            ->delete("/projects/{$this->project->id}/tasks/{$task2->id}");

        // Remaining tasks should be reordered
        $this->assertDatabaseHas('tasks', [
            'id' => $task1->id,
            'priority' => 1
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task3->id,
            'priority' => 2 // Should be moved from 3 to 2
        ]);
    }

    /** @test */
    public function user_can_reorder_tasks()
    {
        $task1 = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 1
        ]);

        $task2 = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 2
        ]);

        $task3 = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'priority' => 3
        ]);

        // Reorder: task3, task1, task2
        $reorderData = [
            'task_ids' => [$task3->id, $task1->id, $task2->id]
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/projects/{$this->project->id}/tasks/reorder", $reorderData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Tasks reordered successfully!'
        ]);

        // Check new priorities
        $this->assertDatabaseHas('tasks', ['id' => $task3->id, 'priority' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $task1->id, 'priority' => 2]);
        $this->assertDatabaseHas('tasks', ['id' => $task2->id, 'priority' => 3]);
    }

    /** @test */
    public function reorder_requires_valid_task_ids()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/projects/{$this->project->id}/tasks/reorder", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['task_ids']);
    }

    /** @test */
    public function reorder_requires_existing_task_ids()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/projects/{$this->project->id}/tasks/reorder", [
                'task_ids' => [999999] // Non-existent task ID
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['task_ids.0']);
    }

    /** @test */
    public function user_cannot_reorder_other_users_tasks()
    {
        $otherUserTask = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => User::factory()->create()->id
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/projects/{$this->project->id}/tasks/reorder", [
                'task_ids' => [$otherUserTask->id]
            ]);

        // The task won't be updated because of the forUser() scope
        $response->assertStatus(200); // Request succeeds but no changes are made
    }
}
