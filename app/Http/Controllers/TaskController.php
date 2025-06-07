<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="To-Do List API",
 *     version="1.0.0",
 *     description="API para gerenciamento de tarefas por usuário autenticado"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Autenticação via token bearerAuth",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */
class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Listar todas as tarefas do usuário autenticado",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Lista de tarefas retornada com sucesso"),
     *     @OA\Response(response=500, description="Erro interno no servidor")
     * )
     */
    public function getAllTasks()
    {
        try {
            $tasks = $this->taskService->getAllTasks();
            return response()->json(["success" => true, "data" => $tasks, "message" => "Tasks retrieved successfully!"], 200);
        } catch (\Throwable $th) {
            return response()->json(["success" => false, "message" => $th->getMessage(), "data" => null], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Criar uma nova tarefa",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Estudar Laravel"),
     *             @OA\Property(property="description", type="string", example="Ler documentação do Laravel")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tarefa criada com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     summary="Atualizar o status de uma tarefa",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID da tarefa", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="completed", enum={"pending","in_progress","completed"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Tarefa atualizada com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro interno")
     * )
     */
    public function updateTask(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed',
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
            return response()->json(["success" => false, "message" => $th->getMessage(), "data" => null], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tasks/filter",
     *     summary="Filtrar tarefas pelo status",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="Status da tarefa para filtrar",
     *         @OA\Schema(type="string", enum={"pending","in_progress","completed"})
     *     ),
     *     @OA\Response(response=200, description="Tarefas filtradas com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro interno")
     * )
     */
    public function filterByStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed',
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

    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     summary="Deletar uma tarefa pelo ID",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tarefa a ser deletada",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Tarefa deletada com sucesso"),
     *     @OA\Response(response=404, description="Tarefa não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro interno")
     * )
     */
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
