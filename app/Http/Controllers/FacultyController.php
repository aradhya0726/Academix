<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyController extends Controller
{
    public function list(Request $request)
    {
        $faculties = Faculty::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.faculty.list', compact('faculties'));
    }

    public function add()
    {
        return view('admin.faculty.add');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255|unique:faculty,name',
            'status' => 'required|in:0,1',
        ]);

        $data['created_by'] = Auth::user()->name;

        Faculty::create($data);

        return redirect()->route('faculty.list')->with('success', 'Faculty added successfully.');
    }

    public function edit($id)
    {
        $faculty = Faculty::findOrFail($id);

        return view('admin.faculty.edit', compact('faculty'));
    }

    public function update(Request $request, $id)
    {
        $faculty = Faculty::findOrFail($id);

        $data = $request->validate([
            'name'   => 'required|string|max:255|unique:faculty,name,' . $faculty->id,
            'status' => 'required|in:0,1',
        ]);

        $faculty->update($data);

        return redirect()->route('faculty.list')->with('success', 'Faculty updated successfully.');
    }

    public function delete($id)
    {
        $faculty = Faculty::findOrFail($id);

        if (ClassModel::where('faculty_id', $faculty->id)->exists()) {
            return redirect()->route('faculty.list')->with('error', 'Cannot delete: this faculty is used by one or more classes.');
        }

        $faculty->delete();

        return redirect()->route('faculty.list')->with('success', 'Faculty deleted successfully.');
    }
}
