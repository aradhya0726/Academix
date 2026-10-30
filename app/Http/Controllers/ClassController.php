<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function list(Request $request)
    {
        $classes = ClassModel::with('faculty')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q) use ($search) {
                    $q->whereHas('faculty', fn ($f) => $f->where('name', 'like', "%{$search}%"))
                      ->orWhere('semester', 'like', "%{$search}%")
                      ->orWhere('created_by', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.class.list', compact('classes'));
    }

    public function add()
    {
        $faculties = Faculty::where('status', 0)->orderBy('name')->get();
        $semesters = ClassModel::SEMESTERS;

        return view('admin.class.add', compact('faculties', 'semesters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'faculty_id' => 'required|exists:faculty,id',
            'semester'   => 'required|integer|in:' . implode(',', ClassModel::SEMESTERS),
            'status'     => 'required|in:0,1',
        ]);

        $data['created_by'] = Auth::user()->name;

        ClassModel::create($data);

        return redirect()->route('class.list')->with('success', 'Class added successfully.');
    }

    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);

        // active faculties, plus the saved one even if it was made inactive later
        $faculties = Faculty::where('status', 0)->orWhere('id', $class->faculty_id)->orderBy('name')->get();
        $semesters = ClassModel::SEMESTERS;

        return view('admin.class.edit', compact('class', 'faculties', 'semesters'));
    }

    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $data = $request->validate([
            'faculty_id' => 'required|exists:faculty,id',
            'semester'   => 'required|integer|in:' . implode(',', ClassModel::SEMESTERS),
            'status'     => 'required|in:0,1',
        ]);

        $class->update($data);

        return redirect()->route('class.list')->with('success', 'Class updated successfully.');
    }

    public function delete($id)
    {
        ClassModel::findOrFail($id)->delete();

        return redirect()->route('class.list')->with('success', 'Class deleted successfully.');
    }
}
