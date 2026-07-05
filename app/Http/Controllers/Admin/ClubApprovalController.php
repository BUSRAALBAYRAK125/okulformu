<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubApprovalController extends Controller
{
    public function pending()
{
    abort_unless(auth()->check() && auth()->user()->user_type === 'admin', 403);

    $clubs = Club::query()
        ->with(['creator'])
        ->where('status', 'pending')
        ->latest()
        ->get()
        ->map(function ($club) {
            $creatorFullName = trim(($club->creator->name ?? '') . ' ' . ($club->creator->surname ?? ''));

            return [
                'id' => $club->id,
                'name' => $club->name,
                'short_description' => $club->short_description,
                'description' => $club->description,
                'status' => $club->status,
                'founded_at_label' => $club->founded_at ? $club->founded_at->format('d.m.Y') : '—',
                'created_at_label' => $club->created_at ? $club->created_at->format('d.m.Y H:i') : '—',
                'creator_name' => $creatorFullName !== ''
                    ? $creatorFullName
                    : ($club->creator->username ?? $club->creator->email ?? 'Kullanıcı'),
            ];
        });

    return view('admin.clubs.pending', [
        'clubs' => $clubs,
        'pendingCount' => $clubs->count(),
    ]);
}

    public function approve(Club $club)
    {
        abort_unless(auth()->check() && auth()->user()->user_type === 'admin', 403);

        $club->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
            'published_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Kulüp başvurusu onaylandı.');
    }

    public function reject(Request $request, Club $club)
    {
        abort_unless(auth()->check() && auth()->user()->user_type === 'admin', 403);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $club->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
            'published_at' => null,
        ]);

        return back()->with('success', 'Kulüp başvurusu reddedildi.');
    }
}