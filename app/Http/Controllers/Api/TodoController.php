<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TodoFormRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TodoResource::collection(Todo::with('user')->where('user_id', Auth::id())->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TodoFormRequest $request)
    {
        try {
            $validated = $request->validated();

            $new_todo = new Todo();
            $new_todo->title = mb_strtolower($validated['title']);
            $new_todo->user_id = Auth::id();

            if($request->hasFile('image')){
                $new_todo->image = $request->file('image')->store('todos', 'public');
            }

            $new_todo->save();

            return response()->json([
                'data' => $new_todo,
                'message' => 'Todo saved successfully'
            ], Response::HTTP_CREATED);
        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex,
                'message' => 'There was an error saving the data, try again',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        $todo->image = $todo->image ? Storage::url($todo->image) : Null;
        return response()->json($todo, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TodoFormRequest $request, Todo $todo)
    {
        try {
            $validated = $request->validated();
            if($request->hasFile('image')){
                $todo->checkExistAndDeleteImage();
                $image_name = $request->file('image')->store('todos', 'public');
                $todo->image = $image_name;
            }
            $todo->title = $validated['title'];
            $todo->save();

            return response()->json([
                'data' => $todo,
                'message' => 'Todo saved successfully'
            ], Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex,
                'message' => 'There was an error updating the data, try again',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        try {
            $todo->checkExistAndDeleteImage();
            $todo->delete();

            return response()->json(['message' => 'Todo deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex,
                'message' => 'There was an error deleting the data, try again',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateStatus(Todo $todo){
        $todo->status = !$todo->status;
        $todo->save();
        $message = $todo->status ? 'Todo already completed' :  'Todo uncompleted';
        return response()->json(['data' => $todo, 'message' => $message], Response::HTTP_NO_CONTENT);
    }
}
