<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ProjectRessource;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectController extends Controller
{

    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*
        return response()->json([
            'message' => 'Salut App',
        ], 200);
        */

        /*
        return response()->json([
            Project::all()
        ], 200);
        */
//-------------------------------------------------------------------------------------------------------
        //retrieve the list of projects with a date older than the current date
        //$projects = Project::whereDate('start_date', '>', '2024-05-10')->get();
        //$projects = Project::whereDate('end_date', '>', '2024-05-10')->get();

        //$projects = Project::whereBetween('start_date', ['2024-01-25', '2024-03-15'] )->get();
//-------------------------------------------------------------------------------------------------------

        //
        //$projects = Project::orderBy('created_at', 'DESC')->get();


        //$projects = Project::paginate(3);

        //$projects = Project::where('user_id', Auth::user()->id)->get();

        return response()->json(
            ProjectRessource::collection(Project::where('user_id', Auth::user()->id)->get())
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        //
        $request->validated($request->all());

        $image = $request->image;

        if($image != null && !$image->getError()){
            $image = $request->image->store('asset', 'public');
        }

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'rate' => $request->rate,
            'user_id' => Auth::user()->id,
            'image' => $image
        ]);

        return $this->successResponse($project, 'Project créé avec succes');

        /*
        return response()->json([
            'success' => true,
            'message' => 'Project créé avec succes'
        ], 200);
        */
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
        //if(!Gate::allows('access', $project)){
        //    return $this->unauthorizedResponse("Vous n'êtes pas autorisé à accèder");
        //}

        if(Auth::user()->cannot('view', $project)){
            return $this->unauthorizedResponse("Vous n'êtes pas autorisé à accèder");
        }

        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        //
        if(!Gate::allows('access', $project)){
            return $this->unauthorizedResponse("Vous n'êtes pas autorisé à accèder");
        }

        $request->validated($request->all());

        $project->update($request->all());

        return $this->successResponse($project, 'Projet modifié avec succes');

        /*
        return response()->json([
            'success' => true,
            'message' => 'Projet modifié avec succes',
            'data' => $project
        ]);
        */


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
        if(!Gate::allows('access', $project)){
            return $this->unauthorizedResponse("Vous n'êtes pas autorisé à accèder");
        }

        $project->delete();

        return $this->successResponse($project, 'Project supprimé avec succes');

        /*
        return response()->json([
            'succes' => true,
            'message' => 'Project supprimé avec succes'
        ]);
        */
    }

    public function search(Request $request){
        $keyword = $request->input('keyword');

        $projects = Project::where('name', 'like', "%$keyword%")->get();

        return response()->json($projects);
    }

}
