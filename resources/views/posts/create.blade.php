@extends('layouts.forum')

@section('title', 'Yeni Gönderi Oluştur')

@section('content')
    <section class="forum-feed-page">
        <div class="forum-feed forum-feed--detail">
            <article class="forum-post-card forum-post-card--reddit forum-post-card--detail">
                <div class="forum-post-card__meta">
                    <span class="forum-post-card__category">Yeni Gönderi</span>
                </div>

                <h1 class="forum-post-card__title">Gönderi Oluştur</h1>

                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">Kategori seç</option>

                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($canSelectCourse)
                        <div class="form-group">
                            <label for="course_id" class="form-label">Ders (isteğe bağlı)</label>
                            <select name="course_id" id="course_id" class="form-control">
                                <option value="">Ders seç</option>

                                @foreach($courses as $course)
                                    <option
                                        value="{{ $course->id }}"
                                        {{ old('course_id') == $course->id ? 'selected' : '' }}
                                    >
                                        {{ $course->name }}{{ $course->code ? ' (' . $course->code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="info-text">
                                Akademik personel isterse gönderiyi belirli bir derse bağlayabilir.
                            </div>

                            @error('course_id')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if($clubs->count())
                        <div class="form-group">
                            <label for="club_id" class="form-label">Kulüp (isteğe bağlı)</label>
                            <select name="club_id" id="club_id" class="form-control">
                                <option value="">Kulüp seçmeden devam et</option>

                                @foreach($clubs as $club)
                                    <option
                                        value="{{ $club->id }}"
                                        {{ $selectedClubId == $club->id ? 'selected' : '' }}
                                    >
                                        {{ $club->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="info-text">
                                Bir kulüp seçersen bu gönderi sadece ilgili kulüp içinde görünür.
                            </div>

                            @error('club_id')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="title" class="form-label">Başlık</label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            class="form-control"
                            placeholder="Gönderi başlığını yaz"
                            value="{{ old('title') }}"
                        >

                        @error('title')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="body" class="form-label">İçerik</label>
                        <textarea
                            name="body"
                            id="body"
                            rows="8"
                            class="form-control"
                            placeholder="Gönderi içeriğini yaz"
                        >{{ old('body') }}</textarea>

                        @error('body')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="attachments" class="form-label">Dosya Eki</label>
                        <input
                            type="file"
                            name="attachments[]"
                            id="attachments"
                            class="form-control"
                            multiple
                            accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                        >

                        <div id="attachmentPreview" class="post-attachment-preview"></div>

                        <div class="info-text">
                            Resim, PDF, Word ve Excel dosyaları yükleyebilirsin. Birden fazla dosya seçebilirsin.
                        </div>

                        @error('attachments')
                            <div class="error-text">{{ $message }}</div>
                        @enderror

                        @error('attachments.*')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Gönderiyi Kaydet
                    </button>
                </form>
            </article>
        </div>
    </section>
@endsection