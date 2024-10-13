<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        $todos = Todo::all();

        return response()->json($todos, 200);
    }

    public function show($id): JsonResponse
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($todo, 200);
    }

    public function update(TodoRequest $request, $id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        $todo->update($request->validated());

        response()->json($todo, 200);
    }

    public function store(TodoRequest $request): JsonResponse
    {
        $todo = Todo::create($request->validated());

        return response()->json($todo, Response::HTTP_CREATED);
    }

    public function destroy($id): JsonResponse
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'No todo found'], Response::HTTP_NOT_FOUND);
        }

        $todo->delete();

        return response()->json($todo, Response::HTTP_NO_CONTENT);
    }
}
