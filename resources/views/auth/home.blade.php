@extends('layouts.forum')

@section('title', 'Forum Ana Sayfa | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed">
            <div class="forum-feed__toolbar">
                <div class="forum-feed__filters">
                    <button type="button" class="forum-feed__filter forum-feed__filter--active">En Yeni</button>
                    <button type="button" class="forum-feed__filter">Popüler</button>
                    <button type="button" class="forum-feed__filter">Takip Edilenler</button>
                </div>
            </div>

            <div class="forum-post-list">
                @forelse($posts as $post)
                    @include('posts.partials.post-card', [
                        'post' => $post,
                        'postBadgeText' => null,
                        'postStatusText' => null,
                        'postRejectionReason' => null,
                        'disablePostLink' => false,
                        'hideSaveAction' => false,
                        'showCommentForm' => true,
                        'showPinAction' => auth()->check() && auth()->user()->user_type === 'admin',
                        'showToggleActiveAction' => auth()->check() && (auth()->id() === $post->user_id || auth()->user()->user_type === 'admin'),
                    ])
                @empty
                    <article class="forum-post-card forum-post-card--reddit">
                        <h2 class="forum-post-card__title">Henüz yayınlanmış gönderi yok</h2>
                        <p class="forum-post-card__text">
                            İlk onaylı gönderi geldiğinde burada görünecek.
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