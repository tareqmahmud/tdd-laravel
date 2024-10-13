<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public $todo;
    public $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->todo = Todo::factory()->create();
        $this->task = Task::factory()->create([
            'title'   => 'Task from factory',
            'todo_id' => $this->todo->id
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    public function test_add_new_task(): void
    {
        $response = $this->postJson(route('tasks.store'), [
            'title'   => 'Testing new task creation',
            'todo_id' => $this->todo->id
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'title'   => 'Testing new task creation',
            'todo_id' => $this->todo->id
        ]);
    }

    public function test_add_new_tasks_todo_id_not_found(): void
    {
        $this->withExceptionHandling();

        $response = $this->postJson(route('tasks.store'), [
            'title'   => 'Testing new task creation',
            'todo_id' => $this->todo->id + fake()->randomNumber(),
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('todo_id');
    }

    public function test_add_new_tasks_todo_id_missing(): void
    {
        $this->withExceptionHandling();

        $response = $this->postJson(route('tasks.store'), [
            'title' => 'Testing new task creation',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('todo_id');
    }

    public function test_add_new_tasks_title_missing(): void
    {
        $this->withExceptionHandling();

        $response = $this->postJson(route('tasks.store'), [
            'todo_id' => $this->todo->id,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('title');
    }

    public function test_update_existing_task(): void
    {
        $response = $this->patchJson(route('tasks.update', $this->task->id), [
            'title'   => 'Task update testing',
            'todo_id' => $this->todo->id
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('tasks', [
            'title'   => 'Task update testing',
            'todo_id' => $this->todo->id
        ]);
    }

    public function test_update_tasks_todo_id_not_found(): void
    {
        $this->withExceptionHandling();

        $response = $this->patchJson(route('tasks.update', $this->task->id), [
            'title'   => 'Testing new task creation',
            'todo_id' => $this->todo->id + fake()->randomNumber(),
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('todo_id');
    }

    public function test_update_tasks_todo_id_missing(): void
    {
        $this->withExceptionHandling();

        $response = $this->patchJson(route('tasks.update', $this->task->id), [
            'title' => 'Testing new task creation',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('todo_id');
    }

    public function test_update_tasks_title_missing(): void
    {
        $this->withExceptionHandling();

        $response = $this->patchJson(route('tasks.update', $this->task->id), [
            'todo_id' => $this->todo->id,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('title');
    }

    public function test_get_all_tasks(): void
    {
        $response = $this->getJson(route('tasks.index'));

        $response->assertOk();

        $this->assertGreaterThanOrEqual(1, count($response->json()));
    }

    public function test_show_single_task_not_found(): void
    {
        $response = $this->getJson(route('tasks.show', $this->task->id + fake()->randomNumber()));

        $response->assertNotFound();
    }

    public function test_show_single_task(): void
    {
        $response = $this->getJson(route('tasks.show', $this->task->id));

        $response->assertOk();

        $response->assertJsonFragment([
            'todo_id' => $this->todo->id,
        ]);
    }

    public function test_delete_task_not_found(): void
    {
        $response = $this->deleteJson(route('tasks.show', $this->task->id + fake()->randomNumber()));

        $response->assertNotFound();
    }

    public function test_delete_single_task(): void
    {
        $response = $this->deleteJson(route('tasks.destroy', $this->task->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'task_id' => $this->task->id
        ]);
    }
}
