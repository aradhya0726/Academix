<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeFeedController extends Controller
{
    public function index(Request $request)
    {
        $notices = Notice::with(['author', 'classroom.faculty'])
            ->visibleTo(Auth::user())
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

        return view('notices.feed', compact('notices'));
    }

    public function show($id)
    {
        $notice = Notice::with(['author', 'classroom.faculty'])
            ->visibleTo(Auth::user())
            ->findOrFail($id);

        return view('notices.show', compact('notice'));
    }
}