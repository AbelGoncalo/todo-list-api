<?php

namespace App\Repositories;

use App\Models\Task;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log};



class TaskRepository
{
    protected $model;

    public function __construct(Task $task)
    {
        $this->model = $task;
    }


    public function getAllTasks($userId)
    {
        return $this->model::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createTask($data)
    {
        try {

            // Validate the data if necessary
            if (empty($data['title']) || empty($data['user_id'])) {
                throw new \InvalidArgumentException('Title and user_id are required');
            }

            // Optionally, you can set default values for other fields
            $data['description'] = $data['description'] ?? null;
        } catch (\InvalidArgumentException $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return $this->model->create($data);
    }

    public function updateTask($data, $id)
    {
        try {

            $task = $this->model
                ->whereBelongsTo(Auth::user())
                ->where('id', $id)
                ->firstOrFail();

            $task->update($data);

            return $task->fresh();
        } catch (ModelNotFoundException $e) {

            return $e->getMessage();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }




    public function filterByStatus($status)
    {
        try {

            return $this->model
             ->whereBelongsTo(Auth::user())
             ->where('status', $status)->get();
        } catch (\Exception $e) {
            Log::error('Error retrieving tasks by status: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }


    public function updateTaskStatus($id, $status)
    {
        try {
            $task = $this->model->findOrFail($id);
            $task->status = $status;
            $task->save();
            return $task->fresh();
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            return response()->json(['error' => 'Task not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating task status: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function deleteTask($id)
    {
        
        return $this->model
            ->whereBelongsTo(Auth::user())
            ->where('id', $id)
            ->delete();
    }
}
