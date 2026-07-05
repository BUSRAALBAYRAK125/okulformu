<article
    class="forum-post-card forum-post-card--reddit
        {{ in_array($post->user?->user_type, ['admin', 'academic']) ? 'forum-post-card--official' : '' }}
        {{ $post->is_pinned ? 'forum-post-card--pinned' : '' }}"
    id="post-{{ $post->id }}"
>
    <div class="forum-post-card__top">
        <div class="forum-post-card__author">
            <a href="{{ route('profile.show', $post->user) }}" class="forum-avatar forum-avatar--sm" style="text-decoration:none;">
                @if(!empty($post->user?->photo))
                    <img src="{{ asset($post->user->photo) }}" alt="{{ $post->user?->name }}">
                @else
                    <span>{{ mb_substr($post->user?->name ?? 'U', 0, 1) }}</span>
                @endif
            </a>

            <div class="forum-post-card__author-meta">
                <div class="forum-post-card__author-line">
                    <strong>
                        <a href="{{ route('profile.show', $post->user) }}" style="color:inherit; text-decoration:none;">
                            {{ $post->user?->name }} {{ $post->user?->surname }}
                        </a>
                    </strong>

                    @if($post->category)
                        <span class="forum-post-card__dot">•</span>
                        <span>{{ $post->category->name }}</span>
                    @endif

                    @if($post->course)
                        <span class="forum-post-card__dot">•</span>
                        <span>{{ $post->course->name }}</span>
                    @endif

                    <span class="forum-post-card__dot">•</span>
                    <span>{{ $post->published_at?->diffForHumans() ?? $post->created_at->diffForHumans() }}</span>
                </div>

                <span class="forum-post-card__author-type">
                    {{ ucfirst($post->user?->user_type ?? 'kullanıcı') }}
                </span>
            </div>
        </div>
    </div>

    <div class="forum-post-card__content">
        @if($post->is_pinned)
            <div class="forum-post-card__official-badge forum-post-card__official-badge--pinned">
                Sabitlenmiş Gönderi
            </div>
        @endif

        @if(!empty($postBadgeText))
            <div class="forum-post-card__official-badge">
                {{ $postBadgeText }}
            </div>
        @elseif(in_array($post->user?->user_type, ['admin', 'academic']))
            <div class="forum-post-card__official-badge">
                {{ $post->user?->user_type === 'admin' ? 'Yönetim Gönderisi' : 'Akademik Gönderi' }}
            </div>
        @endif

        @if(!empty($postStatusText))
            <div class="forum-post-card__official-badge" style="margin-bottom:10px;">
                {{ $postStatusText }}
            </div>
        @endif

        <h2 class="forum-post-card__title">
            @if(empty($disablePostLink))
                <a href="{{ route('posts.show', $post->slug) }}" class="forum-post-card__title-link">
                    {{ $post->title }}
                </a>
            @else
                <span class="forum-post-card__title-link" style="cursor:default;">
                    {{ $post->title }}
                </span>
            @endif
        </h2>

        @if($post->attachments->count())
            <div class="forum-post-attachments">
                @foreach($post->attachments as $attachment)
                    <a
                        href="{{ asset('storage/' . $attachment->file_path) }}"
                        target="_blank"
                        class="forum-post-attachment forum-post-attachment--{{ $attachment->type }}"
                    >
                        {{ $attachment->original_name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="forum-post-card__text">
            {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 260) }}
        </div>

        @if(!empty($postRejectionReason))
            <div class="forum-post-card__text" style="margin-top:10px; color:#b91c1c;">
                <strong>Red nedeni:</strong> {{ $postRejectionReason }}
            </div>
        @endif
    </div>

    <div class="forum-post-card__actions forum-post-card__actions--reddit">
        <span class="forum-post-card__action forum-post-card__action--icon" title="Görüntülenme">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M2 12C3.8 8.6 7.4 6 12 6s8.2 2.6 10 6c-1.8 3.4-5.4 6-10 6s-8.2-2.6-10-6Z" stroke="currentColor" stroke-width="1.8"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
            </svg>
            <span>{{ $post->view_count }}</span>
        </span>

        @if(empty($disablePostLink))
            <a href="{{ route('posts.show', $post->slug) }}" class="forum-post-card__action forum-post-card__action--icon" title="Yorumlar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
                <span>{{ $post->comments_count }}</span>
            </a>
        @else
            <span class="forum-post-card__action forum-post-card__action--icon" title="Yorumlar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
                <span>{{ $post->comments_count }}</span>
            </span>
        @endif

        @if(empty($hideSaveAction))
            <form action="{{ route('posts.toggleSave', $post) }}" method="POST" style="display:inline-flex;">
                @csrf
                <button
                    type="submit"
                    class="forum-post-card__action forum-post-card__action--icon {{ (int) ($post->is_saved ?? 0) > 0 ? 'forum-post-card__action--saved' : '' }}"
                    title="{{ (int) ($post->is_saved ?? 0) > 0 ? 'Kaydı Kaldır' : 'Kaydet' }}"
                    style="{{ (int) ($post->is_saved ?? 0) > 0 ? 'color:#f59e0b;font-weight:700;' : '' }}"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M7 4h10a1 1 0 0 1 1 1v15l-6-4-6 4V5a1 1 0 0 1 1-1Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                            fill="{{ (int) ($post->is_saved ?? 0) > 0 ? 'currentColor' : 'none' }}"
                        />
                    </svg>
                    <span>{{ (int) ($post->is_saved ?? 0) > 0 ? 'Kaydedildi' : 'Kaydet' }}</span>
                    <span>({{ $post->saved_posts_count ?? 0 }})</span>
                </button>
            </form>
        @endif

        @if(!empty($showCommentForm))
            <details class="forum-comment-box forum-comment-box--inline">
                <summary class="forum-post-card__action forum-post-card__action--icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                    <span>Yorum Yap</span>
                </summary>

                <form action="{{ route('comments.store', $post) }}" method="POST" class="forum-comment-form">
                    @csrf

                    <div class="form-group">
                        <textarea
                            name="body"
                            rows="3"
                            class="form-control"
                            placeholder="Yorumunu yaz..."
                        ></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Yorumu Gönder
                    </button>
                </form>
            </details>
        @endif

        @if(!empty($showPinAction))
            <form action="{{ route('admin.posts.togglePin', $post) }}" method="POST" style="display:inline-flex;">
                @csrf
                <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 17V22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M7 3H17L15 10L18 13H6L9 10L7 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ $post->is_pinned ? 'Sabitlemeyi Kaldır' : 'Sabitle' }}</span>
                </button>
            </form>
        @endif

        @if(!empty($showToggleActiveAction))
            <form action="{{ route('posts.toggleActive', $post) }}" method="POST" style="display:inline-flex;">
                @csrf
                <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3V12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M7.8 5.8A7 7 0 1 0 16.2 5.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <span>{{ $post->is_active ? 'Yayından Kaldır' : 'Yayına Al' }}</span>
                </button>
            </form>
        @endif
    </div>
</article>