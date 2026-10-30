<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesNoticeFiles;
use App\Models\ClassModel;
use App\Models\Notice;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeacherNoticeController extends Controller
{
    use HandlesNoticeFiles;

    /** IDs of classes this teacher teaches */
    private function myClassIds(): array
    {
        return Subject::where('teacher_id', Auth::id())
            ->pluck('class_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function classes()
    {
        return ClassModel::with('faculty')
            ->whereIn('id', $this->myClassIds())
            ->get()
            ->sortBy(fn ($c) => ($c->faculty->name ?? '') . str_pad((string) $c->semester, 3, '0', STR_PAD_LEFT))
            ->values();
    }

    private function rules(): array
    {
        return array_merge([
            'title'    => 'required|string|max:255',
            'message'  => 'required|string',
            'class_id' => ['required', Rule::in($this->myClassIds())],
        ], $this->fileRules());
    }

    private function messages(): array
    {
        return [
            'class_id.required' => 'Please choose a class.',
            'class_id.in'       => 'You can only post to classes you teach.',
            'image.max'         => 'The photo must be 4 MB or smaller.',
            'attachment.max'    => 'The file must be 10 MB or smaller.',
        ];
    }

    private function mine($id): Notice
    {
        return Notice::where('user_id', Auth::id())->findOrFail($id);
    }

    public function list(Request $request)
    {
        $notices = Notice::with('classroom.faculty')
            ->where('user_id', Auth::id())
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.teacher.notice.list', compact('notices'));
    }

    public function add()
    {
        $classes = $this->classes();

        return view('admin.teacher.notice.add', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());

        Notice::create(array_merge([
            'user_id'  => Auth::id(),
            'title'    => trim($data['title']),
            'message'  => trim($data['message']),
            'audience' => 'class',
            'class_id' => $data['class_id'],
        ], $this->applyFiles($request)));

        return redirect()->route('teacher.notice.list')->with('success', 'Notice posted successfully.');
    }

    public function edit($id)
    {
        $notice  = $this->mine($id);
        $classes = $this->classes();

        return view('admin.teacher.notice.edit', compact('notice', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $notice = $this->mine($id);

        $data = $request->validate($this->rules(), $this->messages());

        $notice->update(array_merge([
            'title'    => trim($data['title']),
            'message'  => trim($data['message']),
            'audience' => 'class',
            'class_id' => $data['class_id'],
        ], $this->applyFiles($request, $notice)));

        return redirect()->route('teacher.notice.list')->with('success', 'Notice updated successfully.');
    }

    public function delete($id)
    {
        $notice = $this->mine($id);
        $this->deleteFiles($notice);
        $notice->delete();

        return redirect()->route('teacher.notice.list')->with('success', 'Notice deleted successfully.');
    }
}