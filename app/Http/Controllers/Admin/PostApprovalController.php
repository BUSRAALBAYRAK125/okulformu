<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostApprovalController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->with(['user', 'category', 'course'])
            ->where('status', 'pending')
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('admin.posts.pending', compact('posts'));
    }

    public function approve(Post $post): RedirectResponse
    {
        if ($post->status !== 'pending') {
            return back()->with('error', 'Bu gönderi zaten işlem görmüş.');
        }

        $post->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Gönderi onaylandı ve yayınlandı.');
    }

    public function reject(Request $request, Post $post): RedirectResponse
    {
        if ($post->status !== 'pending') {
            return back()->with('error', 'Bu gönderi zaten işlem görmüş.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $post->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
            'published_at' => null,
        ]);

        return back()->with('success', 'Gönderi reddedildi.');
    }
}