<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
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

    public function store(Request $request)
    {
        $this->authorize('notes-create');

        if (!$request->title && !$request->text){
            return response()->json(['title or text required']);
        }

        $data = [
            'user_id' => auth()->id(),
            'title' => $request->title,
            'text' => $request->text,
        ];
        if (!$request->note_id) {
            $note = Note::create($data);
        } else {
            Note::find($request->note_id)->update($data);
            $note = Note::find($request->note_id);
        }

        return response()->json(['data' => true, 'id' => $note->id]);
    }

    public function delete(Request $request)
    {
        $this->authorize('notes-delete');

        Note::find($request->note_id)->delete();
        return response()->json(['data' => true]);
    }
}
