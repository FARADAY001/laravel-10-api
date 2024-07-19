<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        //
        return response()->json($project->tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        //
        $request->validated($request->all());

        //$task = Task::create($request->all());
        $task = new Task($request->all());

        $project->tasks()->save($task);

        return $this->successResponse($task, 'Tache cree avec succes');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Task $task)
    {
        //
        if($task->project_id != $project->id){
            return $this->errorResponse("Cette tache n'appartient pas à ce pojet");
        }

        return response()->json($task);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        //
        $request->validated($request->all());

        if ($task->project_id != $project->id){
            return $this->errorResponse('Cette tache n\'appartient pas à ce pojet');
        }

        $task->update($request->all());

        return $this->successResponse($task, 'Tache modifiée avec succes');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        //
        if($task->project_id != $project->id){
            return $this->errorResponse('Cette tache n\'appartient pas à ce pojet');
        }

        $task->delete();

        return $this->successResponse($task, 'Tache supprimée avec succes');
    }
}
