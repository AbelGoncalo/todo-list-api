<?php
namespace App\Services;

use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\{Hash,Auth};


class TaskService
{
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function createTask($data)
    {
        try {

            $values = [
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'user_id' => $data['user_id']
            ];

            return $this->taskRepository->createTask($values);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function updateTask($data, $id)
    {
        try {
            $values = [
                'title' => $data['title'],
                'description' => $data['description'] ?? null
            ];

            return $this->taskRepository->updateTask($values, $id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
//
