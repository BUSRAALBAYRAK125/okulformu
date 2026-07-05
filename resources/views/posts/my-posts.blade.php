@extends('layouts.forum')

@section('title', 'Benim Konularım | Forum')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed">
            <div class="forum-feed__toolbar">
                <div class="forum-feed__filters">
                    <button type="button" class="forum-feed__filter forum-feed__filter--active">
                        Benim Konularım
                    </button>
                </div>
            </div>

            <div class="forum-post-list">
                @forelse($posts as $post)
                    <article
                        class="forum-post-card forum-post-card--reddit
                            {{ in_array($post->user?->user_type, ['admin', 'academic']) ? 'forum-post-card--official' : '' }}
                            {{ $post->is_pinned ? 'forum-post-card--pinned' : '' }}"
                        id="post-{{ $post->id }}"
                    >
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

                                        <span class="forum-post-card__dot">•</span>
                                        <span>{{ $post->created_at->diffForHumans() }}</span>
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

                            @if(in_array($post->user?->user_type, ['admin', 'academic']))
                                <div class="forum-post-card__official-badge">
                                    {{ $post->user?->user_type === 'admin' ? 'Yönetim Gönderisi' : 'Akademik Gönderi' }}
                                </div>
                            @endif

                            <div class="forum-post-card__official-badge" style="margin-bottom:10px;">
                                @if($post->status === 'approved')
                                    {{ $post->is_active ? 'Yayında' : 'Yayından Kaldırıldı' }}
                                @elseif($post->status === 'pending')
                                    Onay Bekliyor
                                @elseif($post->status === 'rejected')
                                    Reddedildi
                                @else
                                    {{ ucfirst($post->status ?? 'Bilinmiyor') }}
                                @endif
                            </div>

                            <h2 class="forum-post-card__title">
                                @if($post->status === 'approved' && $post->is_active)
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

                            @if($post->status === 'rejected' && !empty($post->rejection_reason))
                                <div class="forum-post-card__text" style="margin-top:10px; color:#b91c1c;">
                                    <strong>Red nedeni:</strong> {{ $post->rejection_reason }}
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

                            @if($post->status === 'approved' && $post->is_active)
                                <a href="{{ route('posts.show', $post->slug) }}" class="forum-post-card__action forum-post-card__action--icon" title="Yorumlar">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    </svg>
                                    <span>{{ $post->comments_count }}</span>
                                </a>

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
                            @else
                                <span class="forum-post-card__action forum-post-card__action--icon" title="Yorumlar">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M8 18H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-7l-4 3v-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    </svg>
                                    <span>{{ $post->comments_count }}</span>
                                </span>
                            @endif

                            @if(auth()->check() && auth()->user()->user_type === 'admin' && $post->status === 'approved' && $post->is_active)
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
                        </div>
                    </article>
                @empty
                    <article class="forum-post-card forum-post-card--reddit">
                        <h2 class="forum-post-card__title">Henüz konu açmadın</h2>
                        <p class="forum-post-card__text">
                            Yeni konu açtığında burada görünecek.
                        </p>
                    </article>
                @endforelse
            </div>

            @if(method_exists($posts, 'links'))
                <div style="margin-top: 18px;">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection