@php
    $hasSuccess = session()->has('success');
@endphp

@if ($hasSuccess)
    <div class="modal is-open" id="feedback-modal" data-modal="feedback">
        <div class="modal-backdrop"></div>

        <div class="modal-dialog">
            <div class="modal-header">
                <div class="modal-status modal-status-success">✓</div>
                <h2 class="modal-title">İşlem Başarılı</h2>
            </div>

            <div class="modal-body">
                <p>{{ session('success') }}</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close="feedback-modal">
                    Kapat
                </button>
            </div>
        </div>
    </div>
@endif