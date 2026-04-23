<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-library-locked').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();

                const message = this.dataset.message || 'This resource is available to AnthroConnect members only.';
                const reason = this.dataset.reason || 'membership_required';

                const title = reason === 'guest_login_required'
                    ? 'Login required'
                    : 'Members-only resource';

                const titleEl = document.getElementById('libraryRestrictionTitle');
                const messageEl = document.getElementById('libraryRestrictionMessage');

                if (titleEl) titleEl.textContent = title;
                if (messageEl) messageEl.textContent = message;

                const modalEl = document.getElementById('libraryRestrictionModal');

                if (modalEl && window.bootstrap) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    return;
                }
                
                // Fallback if bootstrap is not available (though it should be for modal logic)
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
        });

        document.querySelectorAll('[data-copy-target]').forEach(function (button) {
            button.addEventListener('click', async function () {
                const targetId = this.dataset.copyTarget;
                const target = document.getElementById(targetId);

                if (!target) return;

                try {
                    await navigator.clipboard.writeText(target.textContent.trim());
                    const old = this.innerHTML;
                    this.innerHTML = '<span class="material-symbols-outlined text-sm">check</span> Copied';
                    setTimeout(() => this.innerHTML = old, 1600);
                } catch (error) {
                    alert('Citation: ' + target.textContent.trim());
                }
            });
        });

        // Fullscreen Logic
        const fsBtn = document.getElementById('btn-preview-fullscreen');
        const previewEl = document.getElementById('document-preview');

        if (fsBtn && previewEl) {
            fsBtn.addEventListener('click', function () {
                if (!document.fullscreenElement) {
                    previewEl.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable full-screen mode: ${err.message}`);
                    });
                    fsBtn.innerHTML = '<span class="material-symbols-outlined text-sm">fullscreen_exit</span>';
                } else {
                    document.exitFullscreen();
                    fsBtn.innerHTML = '<span class="material-symbols-outlined text-sm">fullscreen</span>';
                }
            });

            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) {
                    fsBtn.innerHTML = '<span class="material-symbols-outlined text-sm">fullscreen</span>';
                }
            });
        }
    });
</script>
