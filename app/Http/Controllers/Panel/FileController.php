<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $query = File::query();

        // جستجو
        if ($request->has('search') && $request->search != '') {
            $query->where('file_name', 'like', '%' . $request->search . '%');
        }

        // مرتب‌سازی
        if ($request->has('sort')) {
            if ($request->sort == 'folders') {
                $query->orderByRaw("CASE WHEN file_type = 'folder' THEN 0 ELSE 1 END")
                    ->orderBy('file_name');
            } elseif ($request->sort == 'files') {
                $query->orderByRaw("CASE WHEN file_type = 'folder' THEN 1 ELSE 0 END")
                    ->orderBy('file_name');
            }
        }

        // فقط فایل‌های بالایی که parent_folder_id ندارند را انتخاب کنید
        $files = $query->where('parent_folder_id', null)->get();

        // تبدیل تاریخ به شمسی
        foreach ($files as $file) {
            $file->jalali_created_at = Jalalian::fromCarbon($file->created_at)->format('%Y/%m/%d');
        }

        return view('panel.files.index', compact('files'));
    }

    // نمایش فرم آپلود فایل
    public function create()
    {
        return view('panel.files.create');
    }

    // آپلود فایل جدید
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('uploads');

        File::create([
            'user_id' => Auth::id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getClientMimeType(),
            'parent_folder_id' => $request->parent_folder_id,
            'user_role' => Auth::user()->role->name, // ذخیره نقش کاربر
        ]);
        alert()->success('فایل با موفقیت بارگذاری شد', 'بارگذاری فایل');
        return redirect()->route('files.index')->with('success', 'فایل با موفقیت بارگذاری شد.');
    }

    // ایجاد فولدر جدید
    public function createFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
        ]);

        File::create([
            'user_id' => Auth::id(),
            'file_name' => $request->folder_name,
            'file_type' => 'folder',
            'parent_folder_id' => $request->parent_folder_id,
            'user_role' => Auth::user()->role->name,
        ]);
        alert()->success('فولدر با موفقیت ایجاد شد', 'ایجاد فولدر');
        return redirect()->route('files.index')->with('success', 'فولدر با موفقیت ایجاد شد.');
    }

    // حذف فایل
    public function destroy($id)
    {
        $file = File::findOrFail($id);

        if ($file->file_type != 'folder') {
            Storage::delete($file->file_path);
        }

        $file->delete();
        alert()->success('فایل با موفقیت حذف گردید', 'حذف فایل');
        return redirect()->route('files.index')->with('success', 'فایل با موفقیت حذف گردید.');
    }

    // دانلود فایل
    public function download($id)
    {
        $file = File::findOrFail($id);

        if ($file->file_type == 'folder') {
            return redirect()->route('files.index')->with('error', 'شما نمیتوانید فولدر را دانلود کنید.');
        }

        return Storage::download($file->file_path, $file->file_name);
    }
    public function showFolder($folderId)
    {
        // دریافت فولدر اصلی
        $folder = File::findOrFail($folderId);

        // دریافت فایل‌ها و فولدرهای داخل فولدر اصلی
        $files = File::where('parent_folder_id', $folderId)->get();

        return view('panel.files.index', compact('files', 'folder'));
    }
    public function bulkDestroy(Request $request)
    {
        $files = $request->input('files'); // دریافت فایل‌های انتخاب شده

        if ($files) {
            // حذف فایل‌ها از پایگاه داده
            File::destroy($files);

            return redirect()->back()->with('success', 'فایل‌های انتخاب شده با موفقیت حذف شدند.');
        }

        return redirect()->back()->with('error', 'هیچ فایلی انتخاب نشده است.');
    }
    public function getShareLink($id)
    {
        $file = File::findOrFail($id);
        $shareLink = route('files.download', $file->id); // لینک مستقیم دانلود فایل

        return response()->json(['link' => $shareLink]);
    }
}
