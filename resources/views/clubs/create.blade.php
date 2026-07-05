@extends('layouts.forum')

@section('title', 'Kulüp Başvurusu Oluştur')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed">
            <div class="feed-card club-form-card">
                <div class="feed-card__header">
                    <h1 class="club-form-title">Kulüp Başvurusu Oluştur</h1>
                    <p class="club-form-subtitle">
                        Kulüp bilgilerini gir, üyeleri seç ve başvurunu oluştur.
                    </p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success club-form-alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger club-form-alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger club-form-alert">
                        <ul class="club-form-error-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('clubs.store') }}" method="POST" class="club-form">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Kulüp Adı</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                maxlength="255"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label for="founded_at" class="form-label">Kuruluş Tarihi</label>
                            <input
                                type="date"
                                name="founded_at"
                                id="founded_at"
                                class="form-control @error('founded_at') is-invalid @enderror"
                                value="{{ old('founded_at') }}"
                            >
                        </div>

                        <div class="col-12">
                            <label for="short_description" class="form-label">Kısa Açıklama</label>
                            <input
                                type="text"
                                name="short_description"
                                id="short_description"
                                class="form-control @error('short_description') is-invalid @enderror"
                                value="{{ old('short_description') }}"
                                maxlength="255"
                                placeholder="Kulübü kısa ve net anlat"
                            >
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea
                                name="description"
                                id="description"
                                rows="6"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Kulübün amacı, faaliyetleri, hedefleri..."
                            >{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="member_ids" class="form-label">Üyeler</label>
                            <select
                                name="member_ids[]"
                                id="member_ids"
                                class="form-select js-club-members @error('member_ids') is-invalid @enderror @error('member_ids.*') is-invalid @enderror"
                                multiple
                                data-placeholder="Üye seç"
                            >
                                @foreach ($users as $user)
                                    @php
                                        $fullName = trim(($user->name ?? '') . ' ' . ($user->surname ?? ''));
                                        $displayName = $fullName !== '' ? $fullName : ($user->username ?? $user->email ?? 'Kullanıcı');
                                    @endphp

                                    <option
                                        value="{{ $user->id }}"
                                        @selected(in_array($user->id, old('member_ids', [])))
                                    >
                                        {{ $displayName }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="club-form-help">
                                Birden fazla kişi seçebilirsin.
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label for="president_ids" class="form-label">Kulüp Başkanları</label>
                            <select
                                name="president_ids[]"
                                id="president_ids"
                                class="form-select js-club-presidents @error('president_ids') is-invalid @enderror @error('president_ids.*') is-invalid @enderror"
                                multiple
                                data-placeholder="Başkan seç"
                            >
                                @foreach ($users as $user)
                                    @php
                                        $fullName = trim(($user->name ?? '') . ' ' . ($user->surname ?? ''));
                                        $displayName = $fullName !== '' ? $fullName : ($user->username ?? $user->email ?? 'Kullanıcı');
                                    @endphp

                                    <option
                                        value="{{ $user->id }}"
                                        @selected(in_array($user->id, old('president_ids', [])))
                                    >
                                        {{ $displayName }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="club-form-help">
                                Başkan olarak seçilen kişiler aynı zamanda üye listesinde de olmalı.
                            </small>
                        </div>
                    </div>

                    <div class="club-form-actions">
                        <button type="submit" class="btn btn-primary">
                            Başvuruyu Oluştur
                        </button>

                        <a href="{{ url()->previous() }}" class="club-secondary-btn">
                            Geri Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection