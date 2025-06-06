<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    //
    protected $taskService;
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function getAllTasks()
    {
        try {
            $tasks = $this->taskService->getAllTasks(Auth::user()->id);

            return response()->json(["success" => true, "data" => $tasks, "message" => "Tasks retrieved successfully!"], 200);
        } catch (\Throwable $th) {
            return response()->json(["success" => false, "message" => $th->getMessage(), "data" => null], 500);
        }
    }

    public function createTask(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|min:3|max:100',

            ], [
                'title.required' => 'O título é obrigatório.',
                'title.string' => 'O título deve ser um texto.',
                'title.max' => 'O título deve ter no máximo :max caracteres.',

                'description.string' => 'A descrição deve ser um texto.',
                'description.max' => 'A descrição deve ter no máximo :max caracteres.',
            ]);

            if ($validator->fails()) {
                return response()->json(["success" => false, "message" => $validator->errors()], 422);
            }


            $task = $this->taskService->createTask(array_merge(
                $request->all(),
                ['user_id' => Auth::user()->id]
            ));

            if (!$task) {
                return response()->json(["success" => false, "message" => "Failed to create task"], 422);
            }

            return response()->json(["success" => true, "data" => $task, "message" => "Task created!"], 201);
        } catch (\Throwable $th) {

            return response()->json(["success" => false, "message" => $th->getMessage(), "data" => null], 500);
        }
    }

    public function updateTask(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed', // Assuming status can be 'pending' or 'completed'
            ], [
                'status.required' => 'O estado é obrigatório.',
                'status.in' => 'O estado deve ser um dos seguintes: pending, in_progress, completed.',
            ]);

            if ($validator->fails()) {
                return response()->json(["success" => false, "message" => $validator->errors()], 422);
            }

            $task = $this->taskService->updateTask($request->all(), $id);

            if (!$task) {
                return response()->json(["success" => false, "message" => "Failed to update task"], 422);
            }

            return response()->json(["success" => true, "data" => $task, "message" => "Task updated!"], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["success" => false, "message" => $th->getMessage(), "data" => null], 500);
        }
    }

    public function filterByStatus(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed', // Assuming status can be 'pending' or 'completed'
            ], [
                'status.required' => 'O estado é obrigatório.',
                'status.in' => 'O estado deve ser um dos seguintes: pending, in_progress, completed.',
            ]);

            if ($validator->fails()) {
                return response()->json(["success" => false, "message" => $validator->errors()], 422);
            }

            $tasks = $this->taskService->filterByStatus($request->all());

            return response()->json(["success" => true, "data" => $tasks, "message" => "Tasks filtered by status successfully!"], 200);

        } catch (\Throwable $th) {

            return response()->json(["success" => false, "message" => $th->getMessage(), "data" => null], 500);
        }
    }

    public function deleteTask($id)
    {
        try {

            $validator = Validator::make(['id' => $id], [

                'id' => 'required|integer|exists:tasks,id',
            ], [
                'id.required' => 'O ID da tarefa é obrigatório.',
                'id.integer' => 'O ID da tarefa deve ser um número inteiro.',
                'id.exists' => 'A tarefa com o ID fornecido não existe.',
            ]);
            
            if ($validator->fails()) {
                return response()->json(["success" => false, "message" => $validator->errors()], 422);
            }
            $task = $this->taskService->deleteTask($id);

            if (!$task) {
                return response()->json(["success" => false, "message" => "Task not found!"], 404);
            }

            return response()->json(["success" => true, "message" => "Task deleted successfully!"], 200);
        } catch (\Throwable $th) {
            return response()->json(["success" => false, "message" => $th->getMessage(), "data" => null], 500);
        }
    }
}
