<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendingApprovalController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.approvals.index', compact('users'));
    }

    public function approve(User $user): RedirectResponse
    {
        if ($user->approval_status !== 'pending') {
            return back()->with('error', 'Bu kullanıcı zaten işlem görmüş.');
        }

        $user->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reject_reason' => null,
        ]);

        return back()->with('success', 'Kullanıcı onaylandı.');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        if ($user->approval_status !== 'pending') {
            return back()->with('error', 'Bu kullanıcı zaten işlem görmüş.');
        }

        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:1000'],
        ]);

        $user->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reject_reason' => $validated['reject_reason'],
        ]);

        return back()->with('success', 'Kullanıcı reddedildi.');
    }
}