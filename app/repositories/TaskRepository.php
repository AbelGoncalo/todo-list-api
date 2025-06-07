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


    public function getAllTasks()
    {
        return $this->model::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createTask($data)
    {
        // Validate the data if necessary
        if (empty($data['title']) || empty($data['user_id'])) {
            throw new \InvalidArgumentException('Title and user_id are required');
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
        return $this->model
            ->whereBelongsTo(Auth::user())
            ->where('status', $status)->get();
    }


    public function updateTaskStatus($id, $status)
    {
        $task = $this->model->findOrFail($id);
        $task->status = $status;
        $task->save();
        return $task->fresh();
    }

    public function deleteTask($id)
    {
        return $this->model
            ->whereBelongsTo(Auth::user())
            ->where('id', $id)
            ->delete();
    }
}
