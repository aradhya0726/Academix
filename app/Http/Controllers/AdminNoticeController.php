<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminNoticeController extends Controller
{
public function list(Request $request)
{
    $notices = Notice::with(['author', 'classroom.faculty'])
        ->when($request->boolean('mine'), fn ($q) => $q->where('user_id', auth()->id()))
        ->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('admin.notice.list', compact('notices'));
}

    public function add()
    {
        $classes = ClassModel::with('faculty')->get();
        return view('admin.notice.add', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('notices/images', 'public');
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $data['attachment']      = $file->store('notices/files', 'public');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $data['user_id'] = auth()->id();

        Notice::create($data);

        return redirect()->route('admin.notice.list')->with('success', 'Notice posted successfully.');
    }

    public function edit($id)
    {
        $notice  = Notice::findOrFail($id);
        $classes = ClassModel::with('faculty')->get();

        return view('admin.notice.edit', compact('notice', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);
        $data   = $this->validated($request);

        if ($request->boolean('remove_image') && $notice->image) {
            Storage::disk('public')->delete($notice->image);
            $data['image'] = null;
        }
        if ($request->hasFile('image')) {
            if ($notice->image) Storage::disk('public')->delete($notice->image);
            $data['image'] = $request->file('image')->store('notices/images', 'public');
        }

        if ($request->boolean('remove_attachment') && $notice->attachment) {
            Storage::disk('public')->delete($notice->attachment);
            $data['attachment'] = null;
            $data['attachment_name'] = null;
        }
        if ($request->hasFile('attachment')) {
            if ($notice->attachment) Storage::disk('public')->delete($notice->attachment);
            $file = $request->file('attachment');
            $data['attachment']      = $file->store('notices/files', 'public');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $notice->update($data);

        return redirect()->route('admin.notice.list')->with('success', 'Notice updated successfully.');
    }

    public function delete($id)
    {
        $notice = Notice::findOrFail($id);

        if ($notice->image)      Storage::disk('public')->delete($notice->image);
        if ($notice->attachment) Storage::disk('public')->delete($notice->attachment);

        $notice->delete();

        return redirect()->route('admin.notice.list')->with('success', 'Notice deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'message'    => 'required|string',
            'audience'   => 'required|in:all,teachers,students,class',
            'class_id'   => 'nullable|required_if:audience,class|exists:class,id',
            'link'       => 'nullable|url|max:2048',
            'image'      => 'nullable|image',
            'attachment' => 'nullable|file',
        ]);

        if ($data['audience'] !== 'class') {
            $data['class_id'] = null;
        }

        return $data;
    }
}