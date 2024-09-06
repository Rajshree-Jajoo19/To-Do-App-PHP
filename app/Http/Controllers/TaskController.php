<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{


    // public function index()
    // {
    //     //  $tasks = Task::all(); // Replace with actual data retrieval
    //     //  return view('tasks.index', compact('tasks'));

    //     // return response()->json($tasks); // Return tasks as JSON

        
    // }

    public function index()
    {
        $tasks = Task::all();
    
        // If the request is an AJAX call, return tasks as JSON
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'tasks' => $tasks
            ]);
        }
    
        // Otherwise, return the view for the initial page load
        return view('tasks.index', compact('tasks'));
    }
    


    // Fetch tasks (AJAX)
    public function getTasks() {
        return response()->json(Task::all());
    }

    // Store a new task
    public function store(Request $request) {
        // Validate input and check for duplicates
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:tasks,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $task = Task::create([
            'name' => $request->name,
            'status' => 'Pending'
        ]);

        return response()->json($task);
    }

    // Update task status to "Done"
    public function update($id) {
        $task = Task::findOrFail($id);
        $task->update(['status' => 'Done']);
        return response()->json($task);
    }

    // Delete a task
   
    public function destroy($id) {
        Task::findOrFail($id)->delete();
        return response()->json(['success' => 'Task deleted']);
    }
}
