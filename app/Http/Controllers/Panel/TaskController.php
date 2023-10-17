<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $this->authorize('tasks-list');

        if (auth()->user()->isAdmin()){
            $tasks = Task::latest()->paginate(30);
        }else{
            $userId = auth()->id();

            $tasks = Task::where('creator_id', $userId)->orWhereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderByDesc('created_at')
            ->paginate(30);
        }

        return view('panel.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $this->authorize('tasks-create');

        return view('panel.tasks.create');
    }

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('tasks-create');

        $task = Task::create([
            'creator_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $this->assignTask($task, $request);

        alert()->success('وظیفه مورد نظر با موفقیت ایجاد شد','ایجاد وظیفه');
        return redirect()->route('tasks.index');
    }

    public function show(Task $task)
    {
        return view('panel.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        // access to tasks-edit permission
        $this->authorize('tasks-edit');

        // edit own task
        $this->authorize('edit-task', $task);

        return view('panel.tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        // access to tasks-edit permission
        $this->authorize('tasks-edit');

        // edit own task
        $this->authorize('edit-task', $task);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $this->assignTask($task, $request);

        alert()->success('وظیفه مورد نظر با موفقیت ویرایش شد','ویرایش وظیفه');
        return redirect()->route('tasks.index');

    }

    public function destroy(Task $task)
    {
        // access to tasks-delete permission
        $this->authorize('tasks-delete');

        // delete own task
        $this->authorize('delete-task', $task);

        $task->delete();
        return back();
    }

    public function changeStatus(Request $request)
    {
        $task = DB::table('task_user')->where(['task_id' => $request->task_id, 'user_id' => auth()->id()]);

        if ($task->first()->status == 'done'){
            $task_status = 'doing';
            $message = 'انجام نشده';
        }else{
            $task_status = 'done';
            $message = 'انجام شده';
        }

        $task->update(['status' => $task_status]);

        return response()->json(['task_status' => $task_status,'message' => $message]);
    }

    private function assignTask(Task $task, $request)
    {
        $task->users()->sync($request->users);
    }
}
