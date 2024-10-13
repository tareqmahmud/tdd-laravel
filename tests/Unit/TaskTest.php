<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\Todo;
use Tests\TestCase;

class TaskTest extends TestCase
{

    public function test_task_is_belongs_todo(): void
    {
        $todo = Todo::factory()->create();
        $tasks = Task::factory()->count(5)->create([
            'todo_id' => $todo->id
        ]);

        $this->assertInstanceOf(Todo::class, $tasks->first()->todo);
    }
}
