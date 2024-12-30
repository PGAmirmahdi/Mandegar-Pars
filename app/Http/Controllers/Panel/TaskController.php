<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Activity;
use App\Models\Task;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TaskController extends Controller
{
    public function index()
    {
        $this->authorize('tasks-list');

        $userId = auth()->id();

        $tasks = Task::where('creator_id', $userId)->orWhereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->orderByDesc('created_at')
        ->paginate(30);

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
// ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ایجاد وظیفه',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . optional(Auth::user()->role)->label . ') وظیفه‌ای به نام "' . $task->title . '" را ایجاد کرده است.',
            'created_at' => now(),
        ]);

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
        // ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش وظیفه',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . optional(Auth::user()->role)->label . ') وظیفه‌ای به نام "' . $task->title . '" را ویرایش کرده است.',
            'created_at' => now(),
        ]);

        alert()->success('وظیفه مورد نظر با موفقیت ویرایش شد','ویرایش وظیفه');
        return redirect()->route('tasks.index');

    }

    public function destroy(Task $task)
    {
        // access to tasks-delete permission
        $this->authorize('tasks-delete');

        // delete own task
        $this->authorize('delete-task', $task);
        // ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف وظیفه',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . optional(Auth::user()->role)->label . ') وظیفه‌ای به نام "' . $task->title . '" را حذف کرده است.',
            'created_at' => now(),
        ]);
        $task->delete();
        return back();
    }

    public function changeStatus(Request $request)
    {
        $task = DB::table('task_user')->where(['task_id' => $request->task_id, 'user_id' => auth()->id()]);

        if ($task->first()->status == 'done'){
            $task_status = 'doing';
            $message = 'انجام نشده';
            $done_at = null;
        }else{
            $task_status = 'done';
            $message = 'انجام شده';
            $done_at = now();
        }

        $task->update(['status' => $task_status, 'done_at' => $done_at]);
// ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'تغییر وضعیت وظیفه',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . optional(Auth::user()->role)->label . ') وضعیت وظیفه "' . $task->first()->title . '" را از "' . ($task->first()->status == 'done' ? 'انجام شده' : 'انجام نشده') . '" به "' . $task_status . '" تغییر داد.',
            'created_at' => now(),
        ]);
        return response()->json(['task_status' => $task_status,'message' => $message]);
    }

    public function addDescription(Request $request)
    {
        $task = DB::table('task_user')->where(['task_id' => $request->task_id, 'user_id' => auth()->id()]);
        $task->update(['description' => $request->description]);
        // ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'تغییر توضیحات وظیفه',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') توضیحات وظیفه "' . $task->first()->title . '" را تغییر داد.',
            'created_at' => now(),
        ]);
    }
    public function getDescription(Request $request)
    {
        $task = DB::table('task_user')->find($request->pivot_id);
        return response()->json(['data' => $task->description]);
    }

    private function assignTask(Task $task, $request)
    {
        $task->users()->sync($request->users);
        $users = User::whereIn('id', $request->users)->get();
        $title = 'وظیفه' ;
        $message = 'وظیفه جدیدی به شما تخصیص داده شد';
        $url = route('tasks.index');
        Notification::send($users, new SendMessage($title,$message, $url));
    }
}
