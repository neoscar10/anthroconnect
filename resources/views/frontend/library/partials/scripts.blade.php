<script>
    function initLibraryScripts() {
        // Fullscreen Logic
        const fsBtn = document.getElementById('btn-preview-fullscreen');
        const previewEl = document.getElementById('document-preview');

        if (fsBtn && previewEl) {
            // Remove existing listener if any (to avoid duplicates)
            const newFsBtn = fsBtn.cloneNode(true);
            fsBtn.parentNode.replaceChild(newFsBtn, fsBtn);

            newFsBtn.addEventListener('click', function () {
                if (!document.fullscreenElement) {
                    previewEl.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable full-screen mode: ${err.message}`);
                    });
                    newFsBtn.innerHTML = '<span class="material-symbols-outlined text-sm">fullscreen_exit</span>';
                } else {
                    document.exitFullscreen();
                    newFsBtn.innerHTML = '<span class="material-symbols-outlined text-sm">fullscreen</span>';
                }
            });
        }
    }

    // Event delegation for locked elements (persistent)
    document.addEventListener('click', function (event) {
        const lockedEl = event.target.closest('.js-library-locked');
        if (lockedEl) {
            event.preventDefault();

            const message = lockedEl.dataset.message || 'This resource is available to AnthroConnect members only.';
            const reason = lockedEl.dataset.reason || 'membership_required';

            const title = reason === 'guest_login_required' ? 'Login required' : 'Members-only resource';

            const titleEl = document.getElementById('libraryRestrictionTitle');
            const messageEl = document.getElementById('libraryRestrictionMessage');

            if (titleEl) titleEl.textContent = title;
            if (messageEl) messageEl.textContent = message;

            const modalEl = document.getElementById('libraryRestrictionModal');
            if (modalEl && window.bootstrap) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }

        // Event delegation for copy buttons
        const copyBtn = event.target.closest('[data-copy-target]');
        if (copyBtn) {
            const targetId = copyBtn.dataset.copyTarget;
            const target = document.getElementById(targetId);

            if (target) {
                navigator.clipboard.writeText(target.textContent.trim()).then(() => {
                    const old = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<span class="material-symbols-outlined text-sm">check</span> Copied';
                    setTimeout(() => copyBtn.innerHTML = old, 1600);
                }).catch(() => {
                    alert('Citation: ' + target.textContent.trim());
                });
            }
        }
    });

    document.addEventListener('fullscreenchange', () => {
        const fsBtn = document.getElementById('btn-preview-fullscreen');
        if (fsBtn) {
            if (!document.fullscreenElement) {
                fsBtn.innerHTML = '<span class="material-symbols-outlined text-sm">fullscreen</span>';
            } else {
                fsBtn.innerHTML = '<span class="material-symbols-outlined text-sm">fullscreen_exit</span>';
            }
        }
    });

    // Run on initial load and after Livewire navigations/updates
    document.addEventListener('DOMContentLoaded', initLibraryScripts);
    document.addEventListener('livewire:navigated', initLibraryScripts);
    document.addEventListener('livewire:load', initLibraryScripts); // For Livewire 2 fallback if any
    
    // Listen for component refreshes
    if (window.Livewire) {
        Livewire.hook('morph.updated', ({ el, component }) => {
            initLibraryScripts();
        });
    }
</script>
