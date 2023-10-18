<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $this->authorize('notes-list');

        $notes = Note::where('user_id', auth()->id())->latest()->paginate(30);
        return view('panel.notes.index', compact('notes'));
    }

    public function create()
    {
        $this->authorize('notes-create');

        return view('panel.notes.create');
    }

    public function store(Request $request)
    {
        $this->authorize('notes-create');

        Note::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'text' => $request->text,
            'status' => $request->status,
        ]);

        alert()->success('یادداشت مورد نظر با موفقیت ایجاد شد','ایجاد یادداشت');
        return redirect()->route('notes.index');
    }

    public function show(Note $note)
    {
        //
    }

    public function edit(Note $note)
    {
        $this->authorize('notes-edit');
        $this->authorize('edit-note', $note);


        return view('panel.notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note)
    {
        $this->authorize('notes-edit');
        $this->authorize('edit-note', $note);

        $note->update([
            'title' => $request->title,
            'text' => $request->text,
            'status' => $request->status,
        ]);

        alert()->success('یادداشت مورد نظر با موفقیت ویرایش شد','ویرایش یادداشت');
        return redirect()->route('notes.index');
    }

    public function destroy(Note $note)
    {
        $this->authorize('notes-delete');

        $note->delete();
        return back();
    }

    public function changeStatus(Request $request)
    {
        $note = Note::find($request->note_id);

        if ($note->status == 'done'){
            $note->update(['status' => 'undone']);
        }else{
            $note->update(['status' => 'done']);
        }
    }
}
