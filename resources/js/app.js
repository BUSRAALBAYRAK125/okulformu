

document.addEventListener('DOMContentLoaded', () => {
    const modals = document.querySelectorAll('.modal');

    modals.forEach((modal) => {
        const closeButtons = modal.querySelectorAll('[data-modal-close]');
        const backdrop = modal.querySelector('.modal-backdrop');

        closeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                modal.classList.remove('is-open');
            });
        });

        if (backdrop) {
            backdrop.addEventListener('click', () => {
                modal.classList.remove('is-open');
            });
        }
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const anchorLinks = document.querySelectorAll('a[href*="#"]');

    anchorLinks.forEach((link) => {
        link.addEventListener('click', function (event) {
            const url = new URL(this.href);
            const hash = url.hash;

            if (!hash) {
                return;
            }

            const target = document.querySelector(hash);

            if (!target) {
                return;
            }

            if (window.location.pathname !== url.pathname) {
                return;
            }

            event.preventDefault();

            const header = document.querySelector('.site-header');
            const headerOffset = header ? header.offsetHeight + 16 : 0;
            const targetPosition =
                target.getBoundingClientRect().top + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth',
            });

            history.pushState(null, '', hash);
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const revealItems = document.querySelectorAll('[data-reveal]');

    if (!revealItems.length) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries, observerInstance) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const delay = entry.target.dataset.revealDelay || 0;

                setTimeout(() => {
                    entry.target.classList.add('is-visible');
                }, Number(delay));

                observerInstance.unobserve(entry.target);
            });
        },
        {
            threshold: 0.12,
        }
    );

    revealItems.forEach((item) => {
        observer.observe(item);
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.querySelector('[data-ajax-form="register"]');

    if (!registerForm) {
        return;
    }

    const submitButton = registerForm.querySelector('[data-submit-button]');
    const submitButtonText = submitButton ? submitButton.textContent : 'Kaydı Tamamla';
    const formErrorBox = registerForm.querySelector('[data-form-error]');

    const clearErrors = () => {
        registerForm.querySelectorAll('[data-error-for]').forEach((item) => {
            item.textContent = '';
        });

        registerForm.querySelectorAll('.is-invalid').forEach((item) => {
            item.classList.remove('is-invalid');
        });

        if (formErrorBox) {
            formErrorBox.textContent = '';
            formErrorBox.hidden = true;
        }
    };

    const markInvalidField = (fieldName, message) => {
        const errorNode = registerForm.querySelector(`[data-error-for="${fieldName}"]`);
        const inputNode = registerForm.querySelector(`[name="${fieldName}"]`);

        if (errorNode) {
            errorNode.textContent = message;
        }

        if (!inputNode) {
            return;
        }

        if (inputNode.type === 'checkbox') {
            const checkWrapper = inputNode.closest('.register-check');

            if (checkWrapper) {
                checkWrapper.classList.add('is-invalid');
            }

            return;
        }

        inputNode.classList.add('is-invalid');
    };

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        clearErrors();

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Gönderiliyor...';
        }

        try {
            const formData = new FormData(registerForm);

            const response = await fetch(registerForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json().catch(() => ({}));

            if (response.status === 422) {
                const errors = data.errors || {};

                Object.keys(errors).forEach((fieldName) => {
                    if (Array.isArray(errors[fieldName]) && errors[fieldName][0]) {
                        markInvalidField(fieldName, errors[fieldName][0]);
                    }
                });

                const firstErrorField = registerForm.querySelector('.is-invalid');

                if (firstErrorField) {
                    firstErrorField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                    });
                }

                return;
            }

            if (!response.ok) {
                throw new Error(data.message || 'Kayıt sırasında beklenmeyen bir hata oluştu.');
            }

            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
        } catch (error) {
            if (formErrorBox) {
                formErrorBox.textContent = error.message || 'Kayıt sırasında beklenmeyen bir hata oluştu.';
                formErrorBox.hidden = false;
            }
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = submitButtonText;
            }
        }
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const closeBtn = document.getElementById('forumSidebarClose');
    const reopenBtn = document.getElementById('forumSidebarReopen');
    const overlay = document.getElementById('forumSidebarOverlay');

    if (!body.classList.contains('forum-body')) {
        return;
    }

    const closeSidebar = () => {
        body.classList.add('forum-sidebar-collapsed');
        body.classList.remove('forum-sidebar-open');
    };

    const openSidebar = () => {
        body.classList.add('forum-sidebar-open');
        body.classList.remove('forum-sidebar-collapsed');
    };

    closeBtn?.addEventListener('click', closeSidebar);
    reopenBtn?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', openSidebar);
});
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('attachments');
    const preview = document.getElementById('attachmentPreview');

    if (!input || !preview) {
        return;
    }

    input.addEventListener('change', () => {
        preview.innerHTML = '';

        const files = Array.from(input.files || []);

        files.forEach((file) => {
            const item = document.createElement('div');
            item.className = 'post-attachment-preview__item';
            item.textContent = file.name;

            preview.appendChild(item);
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const hiddenInput = document.getElementById('interests-hidden-input');
    const chipList = document.getElementById('interest-chip-list');
    const addButton = document.getElementById('interest-chip-add-btn');
    const textInput = document.getElementById('interest-chip-input');

    if (!hiddenInput || !chipList || !addButton || !textInput) {
        return;
    }

    let interests = (hiddenInput.value || '')
        .split(',')
        .map(item => item.trim())
        .map(item => item.replace(/^#/, '').trim())
        .filter(item => item.length > 0);

    interests = [...new Set(interests)];

    function syncHiddenInput() {
        hiddenInput.value = interests.join(', ');
    }

    function renderChips() {
        chipList.innerHTML = '';

        if (!interests.length) {
            const empty = document.createElement('div');
            empty.style.color = '#64748b';
            empty.style.fontSize = '13px';
            empty.textContent = 'Henüz etiket eklenmedi.';
            chipList.appendChild(empty);
            syncHiddenInput();
            return;
        }

        interests.forEach(function (item, index) {
            const chip = document.createElement('div');
            chip.style.display = 'inline-flex';
            chip.style.alignItems = 'center';
            chip.style.gap = '8px';
            chip.style.minHeight = '36px';
            chip.style.padding = '0 10px 0 12px';
            chip.style.borderRadius = '999px';
            chip.style.background = '#eef4ff';
            chip.style.border = '1px solid #cfe0ff';
            chip.style.color = '#1d4ed8';
            chip.style.fontSize = '13px';
            chip.style.fontWeight = '700';

            const label = document.createElement('span');
            label.textContent = '#' + item;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.textContent = '×';
            removeButton.style.border = '0';
            removeButton.style.background = 'transparent';
            removeButton.style.color = '#1d4ed8';
            removeButton.style.fontSize = '18px';
            removeButton.style.lineHeight = '1';
            removeButton.style.cursor = 'pointer';
            removeButton.style.padding = '0';

            removeButton.addEventListener('click', function () {
                interests.splice(index, 1);
                renderChips();
            });

            chip.appendChild(label);
            chip.appendChild(removeButton);
            chipList.appendChild(chip);
        });

        syncHiddenInput();
    }

    function addInterest() {
        let value = textInput.value.trim();

        if (!value) return;

        value = value.replace(/^#/, '').trim();

        if (!value) return;

        if (interests.includes(value)) {
            textInput.value = '';
            return;
        }

        interests.push(value);
        textInput.value = '';
        renderChips();
    }

    addButton.addEventListener('click', addInterest);

    textInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ',') {
            event.preventDefault();
            addInterest();
        }
    });

    renderChips();
});
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
        return;
    }

    const $memberSelect = $('#member_ids');
    const $presidentSelect = $('#president_ids');

    if (!$memberSelect.length || !$presidentSelect.length) {
        return;
    }

    function initClubSelect2() {
        $memberSelect.select2({
            placeholder: $memberSelect.data('placeholder') || 'Üye seç',
            width: '100%',
            closeOnSelect: false
        });

        $presidentSelect.select2({
            placeholder: $presidentSelect.data('placeholder') || 'Başkan seç',
            width: '100%',
            closeOnSelect: false
        });
    }

    function getSelectedMemberIds() {
        return ($memberSelect.val() || []).map(String);
    }

    function syncPresidentOptions() {
        const selectedMemberIds = getSelectedMemberIds();

        $presidentSelect.find('option').each(function () {
            const value = String($(this).val());
            const allowed = selectedMemberIds.includes(value);

            $(this).prop('disabled', !allowed);

            if (!allowed && $(this).prop('selected')) {
                $(this).prop('selected', false);
            }
        });

        $presidentSelect.trigger('change.select2');
    }

    initClubSelect2();
    syncPresidentOptions();

    $memberSelect.on('change', function () {
        syncPresidentOptions();
    });
});