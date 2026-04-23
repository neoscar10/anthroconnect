<div class="modal fade" id="libraryRestrictionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ac-restriction-modal bg-white rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-body p-8 text-center relative">
                <button type="button" class="absolute top-4 right-4 text-stone-400 hover:text-stone-900" data-bs-dismiss="modal" aria-label="Close">
                    <span class="material-symbols-outlined">close</span>
                </button>

                <div class="w-20 h-20 bg-orange-100 text-orange-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">lock</span>
                </div>

                <h3 id="libraryRestrictionTitle" class="font-headline text-2xl font-bold mb-2">Members-only resource</h3>
                <p id="libraryRestrictionMessage" class="text-stone-600 mb-8">This resource is available to AnthroConnect members only.</p>

                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="flex-1 bg-stone-900 text-stone-50 py-4 rounded-xl font-bold hover:bg-stone-800 transition-colors text-center">Login</a>
                        <a href="{{ route('register') }}" class="flex-1 border border-stone-200 py-4 rounded-xl font-bold hover:bg-stone-50 transition-colors text-center">Create Account</a>
                    @else
                        <button type="button" class="w-full bg-stone-900 text-stone-50 py-4 rounded-xl font-bold hover:bg-stone-800 transition-colors" data-bs-dismiss="modal" onclick="window.dispatchEvent(new CustomEvent('open-membership-modal'))">
                            Become a Member
                        </button>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
