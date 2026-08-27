@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Moderatsiya') }}</h2>
    </x-slot>
    {{ Breadcrumbs::render('admin.moderation.index') }}
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300">
                    {{ session('status') }}
                </div>
            @endif

            @forelse ($adverts as $advert)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6" x-data="{ rejecting: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $advert->title }}</div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ $advert->user->name }} · {{ $advert->category->name }}
                                @if ($advert->region)
                                    · {{ $advert->region->getAddress() }}
                                @endif
                                · {{ $advert->price }}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 whitespace-pre-line">{{ $advert->content }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t flex items-center gap-3">
                        <form method="POST" action="{{ route('admin.moderation.approve', $advert) }}"
                              class="flex items-center gap-2">
                            @csrf
                            <input type="date" name="expires_at" value="{{ now()->addMonth()->toDateString() }}"
                                   class="border-gray-300 rounded-md text-sm">
                            <x-primary-button>{{ __('Faollashtirish') }}</x-primary-button>
                        </form>

                        <button type="button" @click="rejecting = !rejecting" class="text-sm text-red-600">
                            {{ __('Rad etish') }}
                        </button>
                    </div>

                    <form x-show="rejecting" method="POST" action="{{ route('admin.moderation.reject', $advert) }}"
                          class="mt-3 flex items-center gap-2">
                        @csrf
                        <input type="text" name="reason" placeholder="{{ __('Rad etish sababi') }}"
                               class="border-gray-300 rounded-md flex-1 text-sm" required>
                        <x-secondary-button type="submit">{{ __('Tasdiqlash') }}</x-secondary-button>
                    </form>
                </div>
            @empty
                <div class="text-gray-500">{{ __('Moderatsiyada e\'lonlar yo\'q.') }}</div>
            @endforelse

            <div>{{ $adverts->links() }}</div>
        </div>
    </div>
</x-app-layout>
