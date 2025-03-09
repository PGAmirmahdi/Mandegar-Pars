<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Activity;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    /**
     * نمایش لیست درخواست مدارک
     */
    public function index(Request $request)
    {
        $documents = DocumentRequest::orderBy('created_at', 'desc')->paginate(10);
        return view('panel.document-request.index', compact('documents'));
    }

    /**
     * نمایش فرم ایجاد درخواست مدارک
     */
    public function create()
    {
        $this->authorize('document-request-create');
        return view('panel.document-request.create');
    }

    /**
     * ذخیره درخواست مدارک جدید
     */
    public function store(StoreDocumentRequest $request)
    {
        $this->authorize('document-request-create');

        $docs = [];
        foreach ($request->document_title as $documentTitle) {
            $docs[] = [
                'document_title' => $documentTitle,
            ];
        }

        DocumentRequest::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'document'    => json_encode($docs),
            'status'      => 'pending',
            'description' => $request->description,
        ]);

        // ثبت فعالیت
        Activity::create([
            'user_id'     => Auth::id(),
            'action'      => 'درخواست مدارک',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') یک درخواست مدارک با عنوان "' . $request->title . '" ایجاد کرد.',
        ]);

        alert()->success('درخواست مدارک مورد نیاز با موفقیت ایجاد شد', 'ایجاد درخواست مدارک');
        return redirect()->route('document_request.index');
    }

    /**
     * نمایش فرم ویرایش درخواست مدارک
     */
    public function edit($id)
    {
        $document = DocumentRequest::findOrFail($id);
        $this->authorize('document-request-edit', $document);
        return view('panel.document-request.edit', compact('document'));
    }

    /**
     * به‌روزرسانی درخواست مدارک
     */
    public function update(StoreDocumentRequest $request, $id)
    {
        $document = DocumentRequest::findOrFail($id);
        $this->authorize('document-request-edit', $document);

        $docs = [];
        foreach ($request->document_title as $documentTitle) {
            $docs[] = [
                'document_title' => $documentTitle,
            ];
        }

        $document->update([
            'title'       => $request->title,
            'document'    => $docs,
            'description' => $request->description,
        ]);

        Activity::create([
            'user_id'     => Auth::id(),
            'action'      => 'ویرایش درخواست مدارک',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') درخواست مدارک با عنوان "' . $request->title . '" را ویرایش کرد.',
        ]);

        alert()->success('درخواست مدارک با موفقیت به‌روز شد', 'ویرایش درخواست مدارک');
        return redirect()->route('document_request.index');
    }

    /**
     * نمایش جزئیات یک درخواست مدارک
     */
    public function show($id)
    {
        $document = DocumentRequest::findOrFail($id);
        // در صورت نیاز به بررسی مجوز مشاهده، می‌توانید از این دستور استفاده کنید:
        $this->authorize('document-request-view', $document);
        return view('panel.document-request.show', compact('document'));
    }

    public function send($id)
    {
        $document = DocumentRequest::findOrFail($id);
        $this->authorize('document-request-list');

        Activity::create([
            'user_id'     => Auth::id(),
            'action'      => 'ارسال مدارک',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') مدارک مربوط به درخواست "' . $document->title . '" را ارسال کرد.',
        ]);

        alert()->success('مدارک با موفقیت ارسال شد', 'ارسال مدارک');
        return redirect()->route('document_request.index');
    }
    public function sendAction(Request $request, $id)
    {
        $document = DocumentRequest::findOrFail($id);
        $this->authorize('document-request-list');

        $document->update([
            'status' => 'sent',
        ]);

        Activity::create([
            'user_id'     => Auth::id(),
            'action'      => 'ارسال مدارک (اکشن)',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') مدارک مربوط به درخواست "' . $document->title . '" را از طریق sendAction ارسال کرد.',
        ]);

        alert()->success('مدارک با موفقیت ارسال شد', 'ارسال مدارک');
        return redirect()->route('document_request.index');
    }
    /**
     * حذف درخواست مدارک
     */
    public function destroy($id)
    {
        $document = DocumentRequest::findOrFail($id);
        $this->authorize('document-request-delete', $document);

        if (auth()->id() != $document->user_id || in_array($document->status, ['sent', 'not-sent'])) {
            alert()->error('امکان حذف این درخواست مدارک برای شما وجود ندارد', 'خطا');
            return redirect()->route('document_request.index');
        }

        $document->delete();

        Activity::create([
            'user_id'     => Auth::id(),
            'action'      => 'حذف درخواست مدارک',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') درخواست مدارک با عنوان "' . $document->title . '" را حذف کرد.',
        ]);

        alert()->success('درخواست مدارک با موفقیت حذف شد', 'حذف درخواست مدارک');
        return redirect()->route('document_request.index');
    }
}
