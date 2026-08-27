<x-app-layout>
    <x-slot name="header">
        @php use Diglactic\Breadcrumbs\Breadcrumbs;$companion = $dialog->isOwner(auth()->id()) ? $dialog->client : $dialog->user; @endphp
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ $dialog->advert->title }} — {{ $companion->name }}
        </h2>
    </x-slot>
    {{ Breadcrumbs::render('cabinet.dialogs.show', $dialog) }}
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <a href="{{ route('adverts.show', $dialog->advert) }}"
               class="text-sm text-indigo-600">{{ __("E'lonni ko'rish") }}</a>

            @foreach ($dialog->messages as $message)
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 {{ $message->user_id === auth()->id() ? 'border-l-4 border-indigo-400' : '' }}">
                    <div class="text-sm text-gray-500 mb-1">
                        {{ $message->user->name }} · {{ $message->created_at->format('d.m.Y H:i') }}
                    </div>
                    <p class="whitespace-pre-line text-gray-800 dark:text-gray-200">{{ $message->message }}</p>
                </div>
            @endforeach

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                <form method="POST" action="{{ route('cabinet.dialogs.messages.store', $dialog) }}">
                    @csrf
                    <textarea name="message" rows="3" class="block w-full border-gray-300 rounded-md"
                              placeholder="{{ __('Xabar yozing...') }}" required>{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2"/>
                    <x-primary-button class="mt-3">{{ __('Yuborish') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
