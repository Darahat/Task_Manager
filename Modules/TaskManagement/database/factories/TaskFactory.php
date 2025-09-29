<?php

namespace Modules\TaskManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\TaskManagement\Models\Task;
use Modules\TaskManagement\Models\Project;
use App\Models\User;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'priority' => $this->faker->numberBetween(1, 10),
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
        ];
    }

    public function withoutProject()
    {
        return $this->state(function (array $attributes) {
            return [
                'project_id' => null,
            ];
        });
    }

    public function withPriority(int $priority)
    {
        return $this->state(function (array $attributes) use ($priority) {
            return [
                'priority' => $priority,
            ];
        });
    }

    public function forUser(User $user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    public function forProject(Project $project)
    {
        return $this->state(function (array $attributes) use ($project) {
            return [
                'project_id' => $project->id,
            ];
        });
    }
}
