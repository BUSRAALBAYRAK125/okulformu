@extends('layouts.forum')

@section('title', $post->title . ' | Forum')

@section('content')
    @php
        $isOfficial = in_array($post->user?->user_type, ['admin', 'academic']);
        $canManagePost = auth()->check() && (auth()->id() === $post->user_id || auth()->user()->user_type === 'admin');
        $isAdmin = auth()->check() && auth()->user()->user_type === 'admin';
        $isSaved = (int) ($post->is_saved ?? 0) > 0;
    @endphp

    <section class="forum-feed-page">
        <div class="forum-feed forum-feed--detail">
            <article class="forum-post-card forum-post-card--reddit forum-post-card--detail {{ $isOfficial ? 'forum-post-card--official' : '' }} {{ $post->is_pinned ? 'forum-post-card--pinned' : '' }}">
                <div class="forum-post-card__top">
                    <div class="forum-post-card__author">
                        <div class="forum-avatar forum-avatar--sm">
                            @if(!empty($post->user?->photo))
                                <img src="{{ asset($post->user->photo) }}" alt="{{ $post->user?->name }}">
                            @else
                                <span>{{ mb_substr($post->user?->name ?? 'U', 0, 1) }}</span>
                            @endif
                        </div>

                        <div class="forum-post-card__author-meta">
                            <div class="forum-post-card__author-line">
                                <strong>{{ $post->user?->name }} {{ $post->user?->surname }}</strong>

                                @if($post->category)
                                    <span class="forum-post-card__dot">•</span>
                                    <span>{{ $post->category->name }}</span>
                                @endif

                                @if($post->course)
                                    <span class="forum-post-card__dot">•</span>
                                    <span>{{ $post->course->name }}</span>
                                @endif
                            </div>

                            <span class="forum-post-card__author-type">
                                {{ ucfirst($post->user?->user_type ?? 'kullanıcı') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="forum-post-detail__meta-line">
                    <span><strong>Yayınlanma:</strong> {{ $post->published_at?->format('d.m.Y H:i') ?? $post->created_at->format('d.m.Y H:i') }}</span>

                    @if($post->reviewer)
                        <span class="forum-post-card__dot">•</span>
                        <span>
                            <strong>Onaylayan:</strong>
                            {{ $post->reviewer->name }} {{ $post->reviewer->surname }}
                        </span>
                    @endif
                </div>

                @if($post->is_pinned)
                    <div class="forum-post-card__official-badge forum-post-card__official-badge--pinned">
                        Sabitlenmiş Gönderi
                    </div>
                @endif

                @if($isOfficial)
                    <div class="forum-post-card__official-badge">
                        {{ $post->user?->user_type === 'admin' ? 'Yönetim Gönderisi' : 'Akademik Gönderi' }}
                    </div>
                @endif

                <h1 class="forum-post-card__title">{{ $post->title }}</h1>

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

                <div class="forum-post-detail__body">
                    {!! nl2br(e($post->body)) !!}
                </div>

                <div class="forum-post-card__actions forum-post-card__actions--reddit">
                    <span class="forum-post-card__action forum-post-card__action--icon" title="Görüntülenme">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M2 12C3.8 8.6 7.4 6 12 6s8.2 2.6 10 6c-1.8 3.4-5.4 6-10 6s-8.2-2.6-10-6Z" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                        <span>{{ $post->view_count }}</span>
                    </span>

                    <span class="forum-post-card__action forum-post-card__action--icon" title="Yorumlar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ $post->comments_count }}</span>
                    </span>

                    <form action="{{ route('posts.toggleSave', $post) }}" method="POST" style="display:inline-flex;">
                        @csrf
                        <button
                            type="submit"
                            class="forum-post-card__action forum-post-card__action--icon {{ $isSaved ? 'forum-post-card__action--saved' : '' }}"
                            title="{{ $isSaved ? 'Kaydı Kaldır' : 'Kaydet' }}"
                            style="{{ $isSaved ? 'color:#f59e0b;font-weight:700;' : '' }}"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M7 4h10a1 1 0 0 1 1 1v15l-6-4-6 4V5a1 1 0 0 1 1-1Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                    fill="{{ $isSaved ? 'currentColor' : 'none' }}"
                                />
                            </svg>
                            <span>{{ $isSaved ? 'Kaydedildi' : 'Kaydet' }}</span>
                            <span>({{ $post->saved_posts_count ?? 0 }})</span>
                        </button>
                    </form>

                    @if($isAdmin)
                        <form action="{{ route('admin.posts.togglePin', $post)}}" method="POST" style="display:inline-flex;">
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

                    @if($canManagePost)
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

            <section class="forum-post-card forum-post-card--reddit forum-post-card--detail">
                <div class="forum-post-detail__comment-box">
                    <h2 class="forum-post-detail__section-title">Yorum Ekle</h2>

                    <form action="{{ route('comments.store', $post) }}" method="POST" class="forum-comment-form">
                        @csrf

                        <div class="form-group">
                            <textarea
                                name="body"
                                rows="4"
                                class="form-control"
                                placeholder="Yorumunu yaz..."
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Yorumu Gönder
                        </button>
                    </form>
                </div>
            </section>

            <section class="forum-post-card forum-post-card--reddit forum-post-card--detail">
                <h2 class="forum-post-detail__section-title">Tüm Yorumlar</h2>

                <div class="forum-comment-thread">
                    @forelse($post->comments as $comment)
                        <article class="forum-comment">
                            <div class="forum-comment__header">
                                <div class="forum-avatar forum-avatar--sm">
                                    @if(!empty($comment->user?->photo))
                                        <img src="{{ asset($comment->user->photo) }}" alt="{{ $comment->user?->name }}">
                                    @else
                                        <span>{{ mb_substr($comment->user?->name ?? 'U', 0, 1) }}</span>
                                    @endif
                                </div>

                                <div class="forum-comment__meta">
                                    <strong>{{ $comment->user?->name }} {{ $comment->user?->surname }}</strong>
                                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="forum-comment__body">
                                {!! nl2br(e($comment->body)) !!}
                            </div>

                            <div class="forum-post-card__actions forum-post-card__actions--reddit forum-comment__actions">
                                <span class="forum-post-card__action forum-post-card__action--icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M7 10V19L12 16L17 19V10" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M8.5 5.5C9.4 4.5 10.5 4 12 4C14.5 4 16 5.7 16 7.8C16 11 12 13 12 13C12 13 8 11 8 7.8C8 7 8.2 6.2 8.5 5.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    </svg>
                                    <span>{{ $comment->likes_count }}</span>
                                </span>

                                <details class="forum-comment-box forum-comment-box--inline">
                                    <summary class="forum-post-card__action forum-post-card__action--icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Yanıtla</span>
                                    </summary>

                                    <form action="{{ route('comments.store', $post) }}" method="POST" class="forum-comment-form">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                                        <div class="form-group">
                                            <textarea
                                                name="body"
                                                rows="3"
                                                class="form-control"
                                                placeholder="Yanıtını yaz..."
                                            ></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            Yanıt Gönder
                                        </button>
                                    </form>
                                </details>
                            </div>

                            @if($comment->replies->count())
                                <div class="forum-comment__replies">
                                    @foreach($comment->replies as $reply)
                                        <article class="forum-comment forum-comment--reply">
                                            <div class="forum-comment__header">
                                                <div class="forum-avatar forum-avatar--sm">
                                                    @if(!empty($reply->user?->photo))
                                                        <img src="{{ asset($reply->user->photo) }}" alt="{{ $reply->user?->name }}">
                                                    @else
                                                        <span>{{ mb_substr($reply->user?->name ?? 'U', 0, 1) }}</span>
                                                    @endif
                                                </div>

                                                <div class="forum-comment__meta">
                                                    <strong>{{ $reply->user?->name }} {{ $reply->user?->surname }}</strong>
                                                    <span>{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>

                                            <div class="forum-comment__body">
                                                {!! nl2br(e($reply->body)) !!}
                                            </div>

                                            <div class="forum-post-card__actions forum-post-card__actions--reddit forum-comment__actions">
                                                <span class="forum-post-card__action forum-post-card__action--icon">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M7 10V19L12 16L17 19V10" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                        <path d="M8.5 5.5C9.4 4.5 10.5 4 12 4C14.5 4 16 5.7 16 7.8C16 11 12 13 12 13C12 13 8 11 8 7.8C8 7 8.2 6.2 8.5 5.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                    </svg>
                                                    <span>{{ $reply->likes_count }}</span>
                                                </span>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <p class="forum-post-card__text">Bu gönderi için henüz yorum yok.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection