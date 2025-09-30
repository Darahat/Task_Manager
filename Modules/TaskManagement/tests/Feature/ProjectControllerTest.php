<?php

namespace Modules\TaskManagement\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\TaskManagement\Models\Project;
use Modules\TaskManagement\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Create a test user for authentication
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/projects');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_authenticated_user_can_view_dashboard()
    {
        $response = $this->actingAs($this->user)->get('/projects');
        $response->assertStatus(200);
        $response->assertViewIs('taskmanagement::projects.dashboard');
    }    /** @test */
    public function dashboard_displays_user_projects_only()
    {
        // Create projects for different users
        $userProject = Project::factory()->create(['user_id' => $this->user->id]);
        $otherUserProject = Project::factory()->create();

        $response = $this->actingAs($this->user)->get('/projects');

        $response->assertStatus(200);
        $response->assertSee($userProject->name);
        $response->assertDontSee($otherUserProject->name);
    }

    /** @test */
    public function test_user_can_create_project()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $projectData = [
            'name' => 'Test Project',
            'description' => 'Test Description',
            'color' => '#3B82F6'
        ];

        $response = $this->postJson('/projects', $projectData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Project created successfully!'
                ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
            'color' => '#3B82F6',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function test_user_can_create_project_via_ajax()
    {
        $projectData = [
            'name' => 'AJAX Project',
            'description' => 'AJAX Description',
            'color' => '#FF5733'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/projects', $projectData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Project created successfully!'
        ]);
        $this->assertDatabaseHas('projects', [
            'name' => 'AJAX Project',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function project_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->user)
            ->post('/projects', []);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function project_name_must_be_required()
    {
        $response = $this->actingAs($this->user)
            ->post('/projects', [
                'description' => 'Test Description'
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function project_color_must_be_valid_hex()
    {
        $response = $this->actingAs($this->user)
            ->post('/projects', [
                'name' => 'Test Project',
                'color' => 'invalid-color'
            ]);

        $response->assertSessionHasErrors(['color']);
    }

    /** @test */
    public function user_can_view_project_create_form()
    {
        $response = $this->actingAs($this->user)->get('/projects/create');
        $response->assertStatus(200);
        $response->assertViewIs('taskmanagement::projects.create');
    }

    /** @test */
    public function user_can_view_project_edit_form()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get("/projects/{$project->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('taskmanagement::projects.edit');
        $response->assertSee($project->name);
    }

    /** @test */
    public function user_cannot_edit_other_users_project()
    {
        $otherUserProject = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->get("/projects/{$otherUserProject->id}/edit");

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_update_project()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated Description',
            'color' => '#00FF00'
        ];

        $response = $this->actingAs($this->user)
            ->put("/projects/{$project->id}", $updateData);

        $response->assertRedirect('/projects');
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name'
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_project()
    {
        $otherUserProject = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->put("/projects/{$otherUserProject->id}", [
                'name' => 'Hacked Project'
            ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_delete_project()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete("/projects/{$project->id}");

        $response->assertRedirect('/projects');
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_project()
    {
        $otherUserProject = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete("/projects/{$otherUserProject->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('projects', ['id' => $otherUserProject->id]);
    }

    /** @test */
    public function deleting_project_sets_associated_tasks_to_no_project()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'user_id' => $this->user->id
        ]);

        $this->actingAs($this->user)
            ->delete("/projects/{$project->id}");

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        // Task should exist but with null project_id
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'project_id' => null
        ]);
    }

    /** @test */
    public function api_returns_user_projects_as_json()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/projects');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'projects' => [
                [
                    'id' => $project->id,
                    'name' => $project->name
                ]
            ]
        ]);
    }

    /** @test */
    public function api_returns_specific_project_details()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'project' => [
                'id' => $project->id,
                'name' => $project->name
            ]
        ]);
    }
}