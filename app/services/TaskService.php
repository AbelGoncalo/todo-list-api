<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\{Hash, Auth};


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


            $values = [
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'user_id' => $data['user_id']
            ];

            return $this->taskRepository->createTask($values);

       
    }

    public function updateTask($data, $id)
    {
        return $this->taskRepository->updateTask($data, $id);
    }

    public function deleteTask($id)
    {
        return $this->taskRepository->deleteTask($id);
    }

    public function filterByStatus($status)
    {
        if (!in_array($status, ['pending', 'in_progress', 'completed'])) {
            return null;
        }
        return $this->taskRepository->filterByStatus($status);
    }
}
//
