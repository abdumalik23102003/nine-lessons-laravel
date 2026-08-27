@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Xabarlar') }}</h2>
    </x-slot>
    {{ Breadcrumbs::render('cabinet.dialogs.index') }}
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-3">
            @forelse ($dialogs as $dialog)
                @php
                    $companion = $dialog->isOwner(auth()->id()) ? $dialog->client : $dialog->user;
                    $unread = $dialog->unreadCountFor(auth()->id());
                @endphp
                <a href="{{ route('cabinet.dialogs.show', $dialog) }}"
                   class="block bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $dialog->advert->title }}</div>
                            <div class="text-sm text-gray-500">{{ $companion->name }}</div>
                        </div>
                        @if ($unread > 0)
                            <span
                                class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">{{ $unread }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="text-gray-500">{{ __("Hozircha xabarlar yo'q.") }}</div>
            @endforelse

            <div>{{ $dialogs->links() }}</div>
        </div>
    </div>
</x-app-layout>
