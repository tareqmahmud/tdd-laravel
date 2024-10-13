<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public $lists;

    public function setUp(): void
    {
        parent::setUp();

        $this->lists = Todo::factory()->count(5)->create();
    }

    public function test_todo_validation_invalid_type(): void
    {
        $this->withExceptionHandling();
        $response = $this->postJson(route('todos.store'), [
            'name' => 123,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('name');
    }

    public function test_todo_validation_without_name(): void
    {
        $this->withExceptionHandling();

        $response = $this->postJson(route('todos.store'), []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('name');
    }

    public function test_add_new_todo(): void
    {
        $response = $this->postJson(route('todos.store'), [
            'name' => 'New Todo',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('todos', [
            'name' => 'New Todo',
        ]);
    }

    public function test_todo_validation_without_name_update(): void
    {
        $this->withExceptionHandling();

        $response = $this->patchJson(route('todos.update', $this->lists[0]->id), []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('name');
    }

    public function test_todo_validation_invalid_type_update(): void
    {
        $this->withExceptionHandling();
        $response = $this->patchJson(route('todos.update', $this->lists[0]->id), [
            'name' => 123,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('name');
    }

    public function test_update_todo_not_found(): void
    {
        $response = $this->patchJson(route('todos.update', count($this->lists) + 1), [
            'name' => 'Update Todo',
        ]);

        $response->assertNotFound();
    }

    public function test_update_todo(): void
    {
        $response = $this->patchJson(route('todos.update', $this->lists[0]->id), [
            'name' => 'Update Todo',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('todos', [
            'name' => 'Update Todo',
        ]);
    }

    public function test_get_all_todos(): void
    {
        $response = $this->getJson(route('todos.index'));

        $response->assertOk();

        $response->assertJsonCount(5);
    }

    public function test_get_single_todos(): void
    {
        $response = $this->getJson(route('todos.show', $this->lists[0]->id));

        $response->assertOk();

        $response->assertJsonFragment([
            'name' => $this->lists[0]->name,
        ]);
    }

    public function test_single_todos_not_found(): void
    {
        $response = $this->getJson(route('todos.show', count($this->lists) + 1));

        $response->assertNotFound();
    }

    public function test_delete_todo(): void
    {
        $response = $this->deleteJson(route('todos.destroy', $this->lists[0]->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing('todos', [
            'name' => $this->lists[0]->name
        ]);
    }

    public function test_delete_todo_not_found(): void
    {
        $response = $this->deleteJson(route('todos.destroy', count($this->lists) + 1));

        $response->assertNotFound();
    }
}
