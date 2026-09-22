<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Telefon raqam') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Telefon raqamingizni kiriting va SMS orqali tasdiqlang.') }}
        </p>
    </header>

    @if (session('status') === 'phone-code-sent')
        <div class="mt-4 text-sm text-green-600 dark:text-green-400">
            {{ __('Tasdiqlash kodi yuborildi.') }}
        </div>
    @endif
    @if (session('status') === 'phone-verified')
        <div class="mt-4 text-sm text-green-600 dark:text-green-400">
            {{ __('Telefon raqam muvaffaqiyatli tasdiqlandi.') }}
        </div>
    @endif

    @if ($user->isPhoneVerified())
        <div class="mt-4 flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
            <span>{{ $user->phone }} — {{ __('tasdiqlangan') }}</span>
        </div>
    @else
        <form method="POST" action="{{ route('profile.phone.request') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <x-input-label for="phone" :value="__('Telefon')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                              :value="old('phone', $user->phone)" placeholder="+998901234567" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
            <x-primary-button>{{ __('Kod yuborish') }}</x-primary-button>
        </form>

        @if ($user->phone)
            <form method="POST" action="{{ route('profile.phone.verify') }}" class="mt-6 space-y-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                @csrf
                <div>
                    <x-input-label for="code" :value="__('SMS kod')" />
                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" maxlength="6" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <x-primary-button>{{ __('Tasdiqlash') }}</x-primary-button>
            </form>
        @endif
    @endif
</section>
