@extends('layouts.forum')

@section('title', 'Onay Bekleyen Kullanıcılar')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed forum-feed--detail">
            <section class="forum-post-card forum-post-card--reddit forum-post-card--detail">
                <h1 class="forum-post-detail__section-title">Onay Bekleyen Kullanıcılar</h1>

                <div class="forum-comment-thread">
                    @forelse($users as $user)
                        <article class="forum-comment">
                            <div class="forum-comment__header">
                                <div class="forum-avatar forum-avatar--sm">
                                    @if(!empty($user->photo))
                                        <img src="{{ asset($user->photo) }}" alt="{{ $user->name }}">
                                    @else
                                        <span>{{ mb_substr($user->name ?? 'U', 0, 1) }}</span>
                                    @endif
                                </div>

                                <div class="forum-comment__meta">
                                    <strong>{{ $user->name }} {{ $user->surname }}</strong>
                                    <span>{{ $user->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="forum-post-card__meta">
                                <span class="forum-post-card__category">{{ ucfirst($user->user_type) }}</span>

                                @if($user->department)
                                    <span class="forum-post-card__dot">•</span>
                                    <span>{{ $user->department }}</span>
                                @endif

                                @if($user->student_no)
                                    <span class="forum-post-card__dot">•</span>
                                    <span>No: {{ $user->student_no }}</span>
                                @endif

                                <span class="forum-post-card__dot">•</span>
                                <span>{{ $user->email }}</span>
                            </div>

                            <div class="forum-post-card__actions forum-post-card__actions--reddit">
                                <span class="forum-post-card__action">Durum: Bekliyor</span>

                                <form action="{{ route('admin.approvals.approve', $user) }}" method="POST" style="display:inline-flex;">
                                    @csrf
                                    <button type="submit" class="forum-post-card__action">
                                        Onayla
                                    </button>
                                </form>

                                <details class="forum-comment-box forum-comment-box--inline">
                                    <summary class="forum-post-card__action">
                                        Reddet
                                    </summary>

                                    <form action="{{ route('admin.approvals.reject', $user) }}" method="POST" class="forum-comment-form">
                                        @csrf

                                        <div class="form-group">
                                            <textarea
                                                name="reject_reason"
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
                        <p class="forum-post-card__text">Şu anda onay bekleyen kullanıcı yok.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection