<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequest;
use App\Models\Leave;
use App\Models\User;
use App\Notifications\SendMessage;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class LeaveController extends Controller
{
    public function index()
    {
        $this->authorize('leaves-list');

        if (Gate::allows('ceo')){
            $leaves = Leave::latest()->paginate(30);
        }else{
            $leaves = Leave::where('user_id', auth()->id())->latest()->paginate(30);
        }

        return view('panel.leaves.index', compact('leaves'));
    }

    public function create()
    {
        $this->authorize('leaves-create');

        return view('panel.leaves.create');
    }

    public function store(StoreLeaveRequest $request)
    {
        $this->authorize('leaves-create');

        // limit daily leave
        if(!auth()->user()->leavesCount() && $request->type == 'daily'){
            alert()->error('سقف مرخصی های روزانه شما تمام شده است','عدم امکان مرخصی');
            return back();
        }
        // end limit daily leave

        Leave::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'desc' => $request->description,
            'type' => $request->type,
            'from_date' => Verta::parse($request->from_date)->toCarbon(),
            'to_date' => Verta::parse($request->to_date)->toCarbon(),
            'from' => $request->from,
            'to' => $request->to,
        ]);

        // send notification to ceo`s
        $roles_id = \App\Models\Role::whereHas('permissions', function ($q){
            $q->where('name','ceo');
        })->pluck('id');
        $ceo_users = User::whereIn('role_id', $roles_id)->get();

        $fullName = auth()->user()->fullName();
        $message = "یک درخواست مرخصی توسط $fullName ثبت شد";
        $url = route('leaves.index');

        Notification::send($ceo_users, new SendMessage($message, $url));
        // end send notification to ceo`s

        alert()->success('درخواست مرخصی شما با موفقیت ثبت شد','درخواست مرخصی');
        return redirect()->route('leaves.index');
    }

    public function show(Leave $leave)
    {
        //
    }

    public function edit(Leave $leave)
    {
        $this->authorize('ceo');

        return view('panel.leaves.edit', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        $this->authorize('ceo');

        if ($request->status != 'pending' && $leave->status != $request->status){
            $status = Leave::STATUS[$request->status];
            $message = "وضعیت درخواست مرخصی شما به $status تغییر یافت";
            $url = route('leaves.index');

            if ($leave->type == 'daily'){
                if ($request->status == 'accept'){
                    // decrease the leaves
                    $from_date = Carbon::parse($leave->from_date);
                    $to_date = Carbon::parse($leave->to_date);
                    $leave_info = DB::table('leave_info')->where('user_id', $leave->user_id);
                    $leave_info->update([
                        'count' => $leave_info->first()->count - ($from_date->diff($to_date)->days == 0 ? 1 : $from_date->diff($to_date)->days),
                    ]);
                    // end decrease the leaves
                }else{
                    // increase the leaves
                    $from_date = Carbon::parse($leave->from_date);
                    $to_date = Carbon::parse($leave->to_date);
                    $leave_info = DB::table('leave_info')->where('user_id', $leave->user_id);
                    $leave_info->update([
                        'count' => $leave_info->first()->count + ($from_date->diff($to_date)->days == 0 ? 1 : $from_date->diff($to_date)->days),
                    ]);
                    // end increase the leaves
                }
            }

            $leave->user->notify(new SendMessage($message, $url));
        }

        $leave->update([
            'acceptor_id' => auth()->id(),
            'answer' => $request->description,
            'status' => $request->status,
            'answer_time' => now(),
        ]);

        alert()->success('وضعیت درخواست مورد نظر با موفقیت تغییر کرد','تعیین وضعیت درخواست');
        return redirect()->route('leaves.index');
    }

    public function destroy(Leave $leave)
    {
        $this->authorize('leaves-delete');

        $leave->delete();
        return back();
    }

    public function getLeaveInfo(Request $request)
    {
        $leave = Leave::find($request->leave_id);
        $data = [
            'title' => $leave->title,
            'desc' => $leave->desc,
            'type' => $leave->type,
            'typeText' => Leave::TYPE[$leave->type],
            'date' => $leave->type == 'daily' ? '('.verta($leave->from_date)->format('Y/m/d').' - '.verta($leave->to_date)->format('Y/m/d').')' : verta($leave->from_date)->format('Y/m/d'),
            'from' => $leave->from ? verta($leave->from)->format('H:i') : null,
            'to' => $leave->to ? verta($leave->to)->format('H:i') : null,
            'acceptor' => $leave->acceptor ? $leave->acceptor->fullName() : null,
            'status' => $leave->status,
            'statusText' => Leave::STATUS[$leave->status],
            'answer' => $leave->answer,
            'answer_time' => verta($leave->answer_time)->format('H:i - Y/m/d'),
        ];

        return response()->json(['data' => $data]);
    }
}
