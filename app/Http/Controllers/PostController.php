<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Post;
use App\Services\PostFeedService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private PostFeedService $postFeedService
    ) {
    }

    public function index(): View
    {
        if (!auth()->check()) {
            return view('home');
        }

        $posts = $this->postFeedService->getHomeFeed(auth()->user());

        return view('auth.home', compact('posts'));
    }

   public function create(Request $request): View
{
    $user = auth()->user();

    $categories = Category::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    $courses = Course::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    $clubs = $this->postFeedService->getAvailableClubsForPosting($user);

    $canSelectCourse = in_array($user->user_type, ['admin', 'academic']);
    $selectedClubId = old('club_id', $request->integer('club_id'));

    return view('posts.create', [
        'categories' => $categories,
        'courses' => $courses,
        'clubs' => $clubs,
        'canSelectCourse' => $canSelectCourse,
        'selectedClubId' => $selectedClubId,
    ]);
}

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'course_id' => [
                in_array($user->user_type, ['admin', 'academic']) ? 'nullable' : 'prohibited',
                'exists:courses,id',
            ],
            'club_id' => ['nullable', 'exists:clubs,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx'],
        ]);

        if (!empty($validated['club_id'])) {
            $canPostInClub = $this->postFeedService->canUserPostInClub(
                $user,
                (int) $validated['club_id']
            );

            if (!$canPostInClub) {
                return back()
                    ->withErrors([
                        'club_id' => 'Bu kulüpte gönderi paylaşma yetkin yok.',
                    ])
                    ->withInput();
            }
        }

        $slug = $this->generateUniqueSlug($validated['title']);

        $isAutoApproved = in_array($user->user_type, ['admin', 'academic']);

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'course_id' => $validated['course_id'] ?? null,
            'club_id' => $validated['club_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'body' => $validated['body'],
            'status' => $isAutoApproved ? 'approved' : 'pending',
            'published_at' => $isAutoApproved ? now() : null,
            'allow_comments' => true,
            'is_active' => true,
        ]);

        foreach ($request->file('attachments', []) as $index => $file) {
            $path = $file->store('post-attachments', 'public');
            $extension = strtolower($file->getClientOriginalExtension());

            $type = match ($extension) {
                'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image',
                'pdf' => 'pdf',
                'doc', 'docx' => 'word',
                'xls', 'xlsx' => 'excel',
                default => 'other',
            };

            $post->attachments()->create([
                'user_id' => $user->id,
                'original_name' => $file->getClientOriginalName(),
                'file_name' => basename($path),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'type' => $type,
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        $redirectUrl = !empty($validated['club_id'])
            ? route('clubs.show', $validated['club_id'])
            : route('home');

        return redirect($redirectUrl)
            ->with(
                'success',
                $isAutoApproved
                    ? 'Gönderi başarıyla yayınlandı.'
                    : 'Gönderi oluşturuldu. Yönetici onayından sonra yayınlanacaktır.'
            );
    }

    public function show(string $slug): View
    {
        $post = $this->postFeedService
            ->buildPostDetailQuery(auth()->user(), $slug)
            ->firstOrFail();

        abort_unless(
            $this->postFeedService->canUserViewPost(auth()->user(), $post),
            403
        );

        $post->increment('view_count');

        return view('posts.show', compact('post'));
    }

    public function saved(): View
    {
        $posts = $this->postFeedService->getSavedFeed(auth()->user());

        return view('posts.saved', compact('posts'));
    }

    public function myPosts(): View
    {
        $posts = $this->postFeedService->getMyPosts(auth()->user());

        return view('posts.my-posts', compact('posts'));
    }

    public function toggleSave(Post $post): RedirectResponse
    {
        if (
            $post->status !== 'approved'
            || !$post->is_active
            || !$this->postFeedService->canUserViewPost(auth()->user(), $post)
        ) {
            abort(404);
        }

        $existingSave = auth()->user()
            ->savedPosts()
            ->where('post_id', $post->id)
            ->first();

        if ($existingSave) {
            $existingSave->delete();

            return back()->with('success', 'Gönderi kayıtlardan kaldırıldı.');
        }

        auth()->user()->savedPosts()->create([
            'post_id' => $post->id,
        ]);

        return back()->with('success', 'Gönderi kaydedildi.');
    }

    public function toggleActive(Post $post): RedirectResponse
    {
        $user = auth()->user();

        if ($user->id !== $post->user_id && $user->user_type !== 'admin') {
            abort(403);
        }

        $post->update([
            'is_active' => !$post->is_active,
        ]);

        return back()->with(
            'success',
            $post->is_active
                ? 'Gönderi tekrar yayına alındı.'
                : 'Gönderi yayından kaldırıldı.'
        );
    }

    public function togglePin(Post $post): RedirectResponse
    {
        if (auth()->user()->user_type !== 'admin') {
            abort(403);
        }

        $isPinned = !$post->is_pinned;

        $post->update([
            'is_pinned' => $isPinned,
            'pinned_at' => $isPinned ? now() : null,
            'pinned_by' => $isPinned ? auth()->id() : null,
        ]);

        return back()->with(
            'success',
            $isPinned
                ? 'Gönderi sabitlendi.'
                : 'Gönderinin sabitlemesi kaldırıldı.'
        );
    }

    private function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);

        if (blank($baseSlug)) {
            $baseSlug = 'gonderi';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}