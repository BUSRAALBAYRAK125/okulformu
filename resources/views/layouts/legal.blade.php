@extends('layouts.guest')

@section('content')
    <section class="legal-page">
        <div class="site-container">
            <div class="legal-card">
                <div class="legal-card__header">
                    <span class="legal-card__eyebrow">@yield('legal_eyebrow', 'Yasal Metin')</span>
                    <h1 class="legal-card__title">@yield('legal_title')</h1>
                    <p class="legal-card__text">@yield('legal_description')</p>
                </div>

                <div class="legal-card__body">
                    @yield('legal_body')
                </div>
            </div>
        </div>
    </section>
@endsection