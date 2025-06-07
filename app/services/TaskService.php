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

    public function getAllTasks()
    {
        return $this->taskRepository->getAllTasks();
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
            return $this->taskRepository->updateTask($data, $id);

        } catch (\Throwable $th) {
            return response()->json(['success' => false,'message' => $th->getMessage() ], 400);
        }
    }

    public function deleteTask($id)
    {
        return $this->taskRepository->deleteTask($id);
    }

    public function filterByStatus($status)
    {
        return $this->taskRepository->filterByStatus($status);
    }
}
//
