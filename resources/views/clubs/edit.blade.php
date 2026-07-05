@extends('layouts.forum')

@section('title', 'Kulüp Düzenle')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed" style="max-width: 900px; margin: 0 auto;">
            <div class="feed-card">
                <div class="feed-card__header" style="margin-bottom: 20px;">
                    <h1 style="margin: 0; font-size: 26px; font-weight: 700;">Kulüp Düzenle</h1>
                    <p style="margin: 8px 0 0; color: #6b7280;">
                        Kulüp bilgilerini güncelle.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 20px;">
                        <ul style="margin: 0; padding-left: 18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('clubs.update', $club) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Kulüp Adı</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $club->name) }}"
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
                                value="{{ old('founded_at', optional($club->founded_at)->format('Y-m-d')) }}"
                            >
                        </div>

                        <div class="col-12">
                            <label for="short_description" class="form-label">Kısa Açıklama</label>
                            <input
                                type="text"
                                name="short_description"
                                id="short_description"
                                class="form-control @error('short_description') is-invalid @enderror"
                                value="{{ old('short_description', $club->short_description) }}"
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
                            >{{ old('description', $club->description) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="member_ids" class="form-label">Üyeler</label>
                            <select
                                name="member_ids[]"
                                id="member_ids"
                                class="form-select @error('member_ids') is-invalid @enderror @error('member_ids.*') is-invalid @enderror"
                                multiple
                                size="10"
                            >
                                @php
                                    $oldMemberIds = old('member_ids', $selectedMemberIds ?? []);
                                @endphp

                                @foreach ($users as $user)
                                    <option
                                        value="{{ $user->id }}"
                                        @selected(in_array($user->id, $oldMemberIds))
                                    >
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small style="display:block; margin-top:6px; color:#6b7280;">
                                Ctrl basılı tutarak birden fazla kişi seçebilirsin.
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label for="president_ids" class="form-label">Kulüp Başkanları</label>
                            <select
                                name="president_ids[]"
                                id="president_ids"
                                class="form-select @error('president_ids') is-invalid @enderror @error('president_ids.*') is-invalid @enderror"
                                multiple
                                size="10"
                            >
                                @php
                                    $oldPresidentIds = old('president_ids', $selectedPresidentIds ?? []);
                                @endphp

                                @foreach ($users as $user)
                                    <option
                                        value="{{ $user->id }}"
                                        @selected(in_array($user->id, $oldPresidentIds))
                                    >
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small style="display:block; margin-top:6px; color:#6b7280;">
                                Başkan olarak seçilen kişiler aynı zamanda üye listesinde de olmalı.
                            </small>
                        </div>
                    </div>

                    <div style="margin-top: 24px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary">
                            Güncelle
                        </button>

                        <a href="{{ route('clubs.show', $club) }}" class="btn btn-outline-secondary">
                            Vazgeç
                        </a>
                    </div>
                </form>

                <hr style="margin: 28px 0;">

                <form action="{{ route('clubs.destroy', $club) }}" method="POST" onsubmit="return confirm('Bu kulübü silmek istediğine emin misin?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-outline-danger">
                        Kulübü Sil
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const memberSelect = document.getElementById('member_ids');
            const presidentSelect = document.getElementById('president_ids');

            function getSelectedValues(select) {
                return Array.from(select.options)
                    .filter(option => option.selected)
                    .map(option => option.value);
            }

            function syncPresidentOptions() {
                const selectedMemberIds = getSelectedValues(memberSelect);

                Array.from(presidentSelect.options).forEach(option => {
                    const shouldShow = selectedMemberIds.includes(option.value);

                    option.hidden = !shouldShow;

                    if (!shouldShow) {
                        option.selected = false;
                    }
                });
            }

            memberSelect.addEventListener('change', syncPresidentOptions);

            syncPresidentOptions();
        });
    </script>
@endsection