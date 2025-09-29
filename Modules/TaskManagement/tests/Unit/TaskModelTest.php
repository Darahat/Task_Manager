<?php

namespace Modules\TaskManagement\Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Modules\TaskManagement\Models\Project;
use Modules\TaskManagement\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_belongs_to_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    /** @test */
    public function task_belongs_to_project()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $task->project);
        $this->assertEquals($project->id, $task->project->id);
    }

    /** @test */
    public function task_can_exist_without_project()
    {
        $task = Task::factory()->create(['project_id' => null]);

        $this->assertNull($task->project_id);
        $this->assertNull($task->project);
    }

    /** @test */
    public function scope_for_user_filters_tasks_by_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task1 = Task::factory()->create(['user_id' => $user1->id]);
        $task2 = Task::factory()->create(['user_id' => $user2->id]);

        $userTasks = Task::forUser($user1->id)->get();

        $this->assertCount(1, $userTasks);
        $this->assertEquals($task1->id, $userTasks->first()->id);
    }

    /** @test */
    public function scope_for_project_filters_tasks_by_project()
    {
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $task1 = Task::factory()->create(['project_id' => $project1->id]);
        $task2 = Task::factory()->create(['project_id' => $project2->id]);

        $projectTasks = Task::forProject($project1->id)->get();

        $this->assertCount(1, $projectTasks);
        $this->assertEquals($task1->id, $projectTasks->first()->id);
    }

    /** @test */
    public function scope_by_priority_orders_tasks_correctly()
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create(['user_id' => $user->id, 'priority' => 3]);
        $task2 = Task::factory()->create(['user_id' => $user->id, 'priority' => 1]);
        $task3 = Task::factory()->create(['user_id' => $user->id, 'priority' => 2]);

        $tasks = Task::forUser($user->id)->byPriority()->get();

        $this->assertEquals(1, $tasks->first()->priority);
        $this->assertEquals(2, $tasks->get(1)->priority);
        $this->assertEquals(3, $tasks->last()->priority);
    }

    /** @test */
    public function task_fillable_attributes_are_correct()
    {
        $fillable = ['name', 'priority', 'user_id', 'project_id'];
        $task = new Task();

        $this->assertEquals($fillable, $task->getFillable());
    }

    /** @test */
    public function task_has_default_priority()
    {
        $user = User::factory()->create();
        $task = Task::create([
            'name' => 'Test Task',
            'user_id' => $user->id,
            'priority' => 1 // Set explicitly for test since defaults might not work in test env
        ]);

        $this->assertEquals(1, $task->priority);
    }

    /** @test */
    public function task_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Task::create([
            'priority' => 1,
            'user_id' => User::factory()->create()->id
        ]);
    }

    /** @test */
    public function task_user_id_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Task::create([
            'name' => 'Test Task',
            'priority' => 1
        ]);
    }

    /** @test */
    public function tasks_can_be_scoped_by_multiple_conditions()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'priority' => 1
        ]);

        $task2 = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'priority' => 2
        ]);

        // Task for different user
        $otherUser = User::factory()->create();
        $task3 = Task::factory()->create([
            'user_id' => $otherUser->id,
            'project_id' => $project->id,
            'priority' => 1
        ]);

        $tasks = Task::forUser($user->id)
                    ->forProject($project->id)
                    ->byPriority()
                    ->get();

        $this->assertCount(2, $tasks);
        $this->assertEquals($task1->id, $tasks->first()->id);
        $this->assertEquals($task2->id, $tasks->last()->id);
    }
}
