@extends('layouts.forum')

@section('title', 'Kulüp Onayları')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed" style="max-width: 1100px; margin: 0 auto;">
            <div class="feed-card">
                <div style="display: flex; justify-content: space-between; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 20px;">
                    <div>
                        <h1 style="margin: 0; font-size: 28px; font-weight: 800;">Kulüp Onayları</h1>
                        <p style="margin: 8px 0 0; color: #6b7280;">
                            Onay bekleyen kulüp başvurularını buradan yönetebilirsin.
                        </p>
                    </div>

                    <span class="badge bg-warning text-dark" style="font-size: 14px; padding: 10px 12px;">
                        {{ $pendingCount }} Bekleyen Başvuru
                    </span>
                </div>

                @if (session('success'))
                    <div class="alert alert-success" style="margin-bottom: 18px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="margin-bottom: 18px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 18px;">
                        <ul style="margin: 0; padding-left: 18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($clubs->count())
                    <div style="display: flex; flex-direction: column; gap: 18px;">
                        @foreach($clubs as $club)
                            <article class="forum-post-card" style="margin: 0;">
                                <div class="forum-post-card__content">
                                    <div style="display: flex; justify-content: space-between; gap: 16px; align-items: start; flex-wrap: wrap;">
                                        <div style="min-width: 0; flex: 1;">
                                            <h2 class="forum-post-card__title" style="margin-bottom: 8px;">
                                                <a href="{{ route('clubs.show', $club['id']) }}" class="forum-post-card__title-link">
                                                    {{ $club['name'] }}
                                                </a>
                                            </h2>

                                            <div class="forum-post-card__author-line" style="margin-bottom: 12px;">
                                                Oluşturan:
                                                <strong>{{ $club['creator_name'] }}</strong>
                                                ·
                                                Başvuru Tarihi:
                                                {{ $club['created_at_label'] }}
                                            </div>

                                            @if($club['short_description'])
                                                <div class="forum-post-card__text" style="margin-bottom: 12px;">
                                                    {{ $club['short_description'] }}
                                                </div>
                                            @endif

                                            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 12px; color: #667085; font-size: 14px;">
                                                <span>
                                                    <strong>Kuruluş:</strong>
                                                    {{ $club['founded_at_label'] }}
                                                </span>

                                                <span>
                                                    <strong>Durum:</strong>
                                                    {{ $club['status'] }}
                                                </span>
                                            </div>

                                            @if($club['description'])
                                                <details style="margin-top: 10px;">
                                                    <summary style="cursor: pointer; color: #344054; font-weight: 600;">
                                                        Detaylı açıklamayı göster
                                                    </summary>

                                                    <div style="margin-top: 10px; color: #344054; line-height: 1.8; white-space: pre-line;">
                                                        {{ $club['description'] }}
                                                    </div>
                                                </details>
                                            @endif
                                        </div>

                                        <div style="width: 320px; max-width: 100%; display: flex; flex-direction: column; gap: 12px;">
                                            <form action="{{ route('admin.clubs.approve', $club['id']) }}" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="btn btn-success w-100">
                                                    Onayla
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.clubs.reject', $club['id']) }}" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <div style="margin-bottom: 10px;">
                                                    <label for="rejection_reason_{{ $club['id'] }}" class="form-label">
                                                        Red Sebebi
                                                    </label>
                                                    <textarea
                                                        name="rejection_reason"
                                                        id="rejection_reason_{{ $club['id'] }}"
                                                        rows="4"
                                                        class="form-control"
                                                        placeholder="Kulübün neden reddedildiğini yaz..."
                                                        required
                                                    ></textarea>
                                                </div>

                                                <button type="submit" class="btn btn-outline-danger w-100">
                                                    Reddet
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="profile-empty-state">
                        Şu anda onay bekleyen kulüp başvurusu yok.
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection