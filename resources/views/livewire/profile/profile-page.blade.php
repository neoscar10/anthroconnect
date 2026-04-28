<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-headline italic font-bold text-slate-900 dark:text-white">Profile Settings</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2">Manage your scholarly identity and security preferences.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Avatar & Quick Info -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-white/5 rounded-[2rem] border border-sand/50 shadow-sm p-8 flex flex-col items-center text-center sticky top-24">
                <div class="relative group">
                    <div class="size-40 rounded-[2.5rem] overflow-hidden border-4 border-white dark:border-slate-800 shadow-xl relative">
                        @if($new_avatar)
                            <img src="{{ $new_avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($avatar)
                            <img src="{{ str_starts_with($avatar, 'http') ? $avatar : Storage::url($avatar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-sand flex items-center justify-center text-primary text-5xl font-headline font-black italic">
                                {{ substr($name, 0, 1) }}
                            </div>
                        @endif
                        
                        <!-- Upload Overlay -->
                        <label for="avatar-upload" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer backdrop-blur-[2px]">
                            <span class="material-symbols-outlined text-white text-3xl">photo_camera</span>
                        </label>
                        <input type="file" id="avatar-upload" wire:model="new_avatar" class="hidden" accept="image/*">
                    </div>
                    @error('new_avatar') <span class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</span> @enderror
                    
                    @if($new_avatar)
                        <div class="absolute -bottom-2 -right-2 bg-primary text-white size-8 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                            <span class="material-symbols-outlined text-sm">check</span>
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <h2 class="text-2xl font-headline italic font-bold text-slate-900 dark:text-white">{{ $name }}</h2>
                    <p class="text-primary text-xs font-bold uppercase tracking-[0.2em] mt-1">Scholarly Contributor</p>
                </div>

                <div class="w-full mt-8 pt-8 border-t border-sand/30 space-y-4">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-widest">Account Type</span>
                        <span class="text-slate-900 dark:text-white font-bold">Scholar</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-widest">Member Since</span>
                        <span class="text-slate-900 dark:text-white font-bold">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                </div>

                @if(session('status') === 'profile-updated')
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.opacity class="mt-4 px-4 py-2 bg-green-500/10 text-green-500 rounded-xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 border border-green-500/20">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Avatar Updated Successfully
                    </div>
                @endif

                @if($new_avatar)
                    <button wire:click="updateProfile" wire:loading.attr="disabled" class="w-full mt-8 py-3 bg-primary text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group">
                        <span wire:loading.remove wire:target="updateProfile" class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm group-hover:rotate-12 transition-transform">save</span>
                            Save New Photo
                        </span>
                        <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                            <div class="animate-spin size-4 border-2 border-white/30 border-t-white rounded-full"></div>
                            Saving...
                        </span>
                    </button>
                    <button wire:click="$set('new_avatar', null)" class="w-full mt-2 py-2 text-slate-400 text-[10px] font-bold uppercase tracking-widest hover:text-red-500 transition-colors">
                        Cancel
                    </button>
                @endif
            </div>
        </div>

        <!-- Main Content: Forms -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Profile Information -->
            <div class="bg-white dark:bg-white/5 rounded-[2rem] border border-sand/50 shadow-sm p-8 sm:p-12">
                <div class="flex items-center gap-3 mb-8">
                    <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Profile Information</h3>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Full Name</label>
                                <span class="text-[9px] text-slate-400 uppercase font-black tracking-tighter bg-sand px-1.5 rounded flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[10px]">lock</span> Verified
                                </span>
                            </div>
                            <input wire:model="name" type="text" readonly class="w-full bg-sand/5 border border-sand/50 rounded-xl px-4 py-3 text-slate-500 cursor-not-allowed transition-all shadow-sm">
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">WhatsApp Number</label>
                                <span class="text-[9px] text-slate-400 uppercase font-black tracking-tighter bg-sand px-1.5 rounded flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[10px]">verified</span> Linked
                                </span>
                            </div>
                            <input wire:model="whatsapp_phone" type="text" readonly class="w-full bg-sand/5 border border-sand/50 rounded-xl px-4 py-3 text-slate-500 cursor-not-allowed transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="p-4 bg-primary/5 rounded-xl border border-primary/10">
                        <p class="text-[10px] text-primary font-bold uppercase tracking-widest leading-relaxed">
                            <span class="material-symbols-outlined text-xs align-middle mr-1">info</span>
                            Your name and phone number are verified for your scholarly identity and cannot be changed here. Contact support if you need to update these details.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Security: Password Change -->
            <div class="bg-white dark:bg-white/5 rounded-[2rem] border border-sand/50 shadow-sm p-8 sm:p-12">
                <div class="flex items-center gap-3 mb-8">
                    <div class="size-10 rounded-xl bg-olive/10 flex items-center justify-center text-olive">
                        <span class="material-symbols-outlined">shield</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Security & Password</h3>
                </div>

                <form wire:submit.prevent="updatePassword" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Current Password</label>
                        <input wire:model="current_password" type="password" class="w-full bg-sand/10 border border-sand rounded-xl px-4 py-3 focus:ring-2 focus:ring-olive/50 text-slate-900 dark:text-white transition-all shadow-inner">
                        @error('current_password') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">New Password</label>
                            <input wire:model="new_password" type="password" class="w-full bg-sand/10 border border-sand rounded-xl px-4 py-3 focus:ring-2 focus:ring-olive/50 text-slate-900 dark:text-white transition-all shadow-inner">
                            @error('new_password') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Confirm New Password</label>
                            <input wire:model="new_password_confirmation" type="password" class="w-full bg-sand/10 border border-sand rounded-xl px-4 py-3 focus:ring-2 focus:ring-olive/50 text-slate-900 dark:text-white transition-all shadow-inner">
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-4">
                        @if(session('status') === 'password-updated')
                            <span class="text-green-500 text-xs font-bold mr-4 flex items-center gap-1" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                Password updated
                            </span>
                        @endif

                        <button type="submit" class="px-8 py-3 bg-olive text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-olive/20 hover:bg-olive/90 transition-all flex items-center gap-2">
                            <span wire:loading wire:target="updatePassword" class="animate-spin text-xs">refresh</span>
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Note: Account Deletion has been removed as requested -->
        </div>
    </div>
</div>
