@extends('layouts.guest')

@section('title', 'Onay Bekleyen Kullanıcılar | Marmara Üniversitesi Bilgisayar ve Öğretim Teknolojileri Bölümü')

@section('content')
    <section class="legal-page">
        <div class="site-container">
            <div class="legal-card">
                <div class="legal-card__header">
                    <span class="legal-card__eyebrow">Yönetim</span>
                    <h1 class="legal-card__title">Onay Bekleyen Kullanıcılar</h1>
                    <p class="legal-card__text">
                        Yönetici onayı bekleyen kullanıcı hesapları aşağıda listelenmektedir.
                    </p>
                </div>

                <div class="legal-card__body">
                    @if ($users->isEmpty())
                        <p>Şu anda onay bekleyen kullanıcı yok.</p>
                    @else
                        <div class="approval-table-wrapper">
                            <table class="approval-table">
                                <thead>
                                    <tr>
                                        <th>Ad Soyad</th>
                                        <th>E-posta</th>
                                        <th>Kullanıcı Tipi</th>
                                        <th>Kayıt Tarihi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->name }} {{ $user->surname }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->user_type }}</td>
                                            <td>{{ $user->created_at?->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection