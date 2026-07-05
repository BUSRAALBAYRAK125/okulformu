@extends('layouts.forum')

@section('title', 'Onay Bekleyen Gönderiler')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed forum-feed--detail">
            <section class="forum-post-card forum-post-card--reddit forum-post-card--detail">
                <h1 class="forum-post-detail__section-title">Onay Bekleyen Gönderiler</h1>

                <div class="forum-comment-thread">
                    @forelse($posts as $post)
                        <article class="forum-comment">
                            <div class="forum-comment__header">
                                <div class="forum-avatar forum-avatar--sm">
                                    @if(!empty($post->user?->photo))
                                        <img src="{{ asset($post->user->photo) }}" alt="{{ $post->user?->name }}">
                                    @else
                                        <span>{{ mb_substr($post->user?->name ?? 'U', 0, 1) }}</span>
                                    @endif
                                </div>

                                <div class="forum-comment__meta">
                                    <strong>{{ $post->user?->name }} {{ $post->user?->surname }}</strong>
                                    <span>{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="forum-post-card__meta">
                                <span class="forum-post-card__category">{{ $post->category?->name }}</span>

                                @if($post->course)
                                    <span class="forum-post-card__dot">•</span>
                                    <span>{{ $post->course->name }}</span>
                                @endif

                                <span class="forum-post-card__dot">•</span>
                                <span>{{ ucfirst($post->user?->user_type ?? 'kullanıcı') }}</span>
                            </div>

                            <h2 class="forum-post-card__title">{{ $post->title }}</h2>

                            <div class="forum-post-card__text">
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 300) }}
                            </div>
<div class="forum-post-card__actions forum-post-card__actions--reddit">
    <span class="forum-post-card__action">Durum: Bekliyor</span>

    <form action="{{ route('admin.posts.approve', $post) }}" method="POST" style="display:inline-flex;">
        @csrf
        <button type="submit" class="forum-post-card__action">
            Onayla
        </button>
    </form>

    <details class="forum-comment-box forum-comment-box--inline">
        <summary class="forum-post-card__action">
            Reddet
        </summary>

        <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="forum-comment-form">
            @csrf

            <div class="form-group">
                <textarea
                    name="rejection_reason"
                    rows="3"
                    class="form-control"
                    placeholder="Red sebebini yaz..."
                ></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Reddi Kaydet
            </button>
        </form>
    </details>
</div>
                        </article>
                    @empty
                        <p class="forum-post-card__text">Şu anda onay bekleyen gönderi yok.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection