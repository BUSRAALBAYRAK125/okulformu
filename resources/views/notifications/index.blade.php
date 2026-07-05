@extends('layouts.forum')

@section('title', 'Bildirimler | Forum')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed forum-feed--detail" style="max-width: 980px; margin: 0 auto;">
            <div class="forum-post-card forum-post-card--reddit" style="padding: 0; overflow: hidden;">
                <div style="background:#10213f; color:#fff; padding:24px 28px 18px;">
                    <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-start;">
                        <div>
                            <h1 style="margin:0 0 8px; font-size:32px; line-height:1.1; font-weight:800;">Bildirimler</h1>
                            <p style="margin:0; color:#cdd8ea; line-height:1.7;">
                                Tüm bildirimlerini buradan görebilir, okundu yapabilir, silebilir ve ilgili sayfaya geçebilirsin.
                            </p>
                        </div>

                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('home') }}"
                               class="forum-post-card__action forum-post-card__action--icon"
                               style="background:rgba(255,255,255,.1); border-color:rgba(255,255,255,.18); color:#fff;">
                                Anasayfaya Dön
                            </a>

                            <form method="POST" action="{{ route('notifications.readAll') }}" style="margin:0;">
                                @csrf
                                <button type="submit"
                                        class="forum-post-card__action forum-post-card__action--icon"
                                        style="background:rgba(255,255,255,.1); border-color:rgba(255,255,255,.18); color:#fff;">
                                    Tümünü Okundu Yap
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div style="margin:20px 28px 0; padding:12px 14px; border:1px solid #bbf7d0; background:#f0fdf4; color:#166534; border-radius:12px;">
                        {{ session('success') }}
                    </div>
                @endif

                <div style="padding:24px 28px;">
                    @forelse($notifications as $notification)
                        <div style="
                            padding:18px 18px 16px;
                            border:1px solid {{ $notification->is_read ? '#d8e1eb' : '#bfd7ff' }};
                            background: {{ $notification->is_read ? '#ffffff' : '#f8fbff' }};
                            border-radius:16px;
                            margin-bottom:14px;
                        ">
                            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap; margin-bottom:8px;">
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                    <strong style="font-size:17px; color:#0f172a;">
                                        {{ $notification->title }}
                                    </strong>

                                    @if(!$notification->is_read)
                                        <span style="display:inline-flex; width:10px; height:10px; border-radius:50%; background:#ef4444;"></span>
                                    @endif
                                </div>

                                <span style="font-size:13px; color:#64748b;">
                                    {{ $notification->created_at?->diffForHumans() }}
                                </span>
                            </div>

                            @if(!empty($notification->body))
                                <div style="color:#475569; line-height:1.7; margin-bottom:10px;">
                                    {{ $notification->body }}
                                </div>
                            @endif

                            <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap;">
                                <span style="font-size:12px; font-weight:700; color:#2563eb; text-transform:capitalize;">
                                    {{ str_replace('_', ' ', $notification->type) }}
                                </span>

                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <a href="{{ route('notifications.go', $notification) }}"
                                       class="forum-post-card__action forum-post-card__action--icon">
                                        İlgili Sayfaya Git
                                    </a>

                                    @if(!$notification->is_read)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
                                                Okundu Yap
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="forum-post-card__action forum-post-card__action--icon">
                                            Sil
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding:22px; border:1px dashed #d8e1eb; border-radius:16px; color:#64748b; background:#f8fafc;">
                            Henüz bildirimin yok.
                        </div>
                    @endforelse

                    @if($notifications->hasPages())
                        <div style="margin-top:20px;">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection