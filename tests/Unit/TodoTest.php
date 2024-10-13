<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\Todo;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoTest extends TestCase
{
    public $todo;
    public $tasks;

    protected function setUp(): void
    {
        parent::setUp();
        $this->todo = Todo::factory()->create();
        $this->tasks = Task::factory()->count(5)->create([
            'todo_id' => $this->todo->id
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    public function test_todo_has_many_tasks(): void
    {
        $this->assertInstanceOf(Task::class, $this->todo->tasks->first());
    }

    public function test_delete_all_tasks_if_todo_delete(): void
    {
        $this->todo->delete();

        $this->assertDatabaseMissing('todos', [
            'id' => $this->todo->id
        ]);

        $this->assertDatabaseMissing('tasks', [
            'todo_id' => $this->todo->id
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $this->tasks->first()->id
        ]);
    }
}
