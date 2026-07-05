@extends('layouts.forum')

@section('title', 'Kaydettiğim Gönderiler | Forum')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed">
            <div class="forum-feed__toolbar">
                <div class="forum-feed__filters">
                    <button type="button" class="forum-feed__filter forum-feed__filter--active">
                        Kaydettiğim Gönderiler
                    </button>
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
                        'showCommentForm' => false,
                        'showPinAction' => auth()->check() && auth()->user()->user_type === 'admin',
                        'showToggleActiveAction' => auth()->check() && (auth()->id() === $post->user_id || auth()->user()->user_type === 'admin'),
                    ])
                @empty
                    <article class="forum-post-card forum-post-card--reddit">
                        <h2 class="forum-post-card__title">Henüz kaydettiğin gönderi yok</h2>
                        <p class="forum-post-card__text">
                            Beğendiğin gönderileri kaydettiğinde burada görünecek.
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