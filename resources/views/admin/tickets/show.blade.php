@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ $ticket->subject }}
            — {{ $ticket->user->name }}</h2>
    </x-slot>
    {{ Breadcrumbs::render('admin.tickets.show', $ticket) }}
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-red-600">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                <div class="text-sm text-gray-500 mb-1">{{ $ticket->user->name }}
                    · {{ $ticket->created_at->format('d.m.Y H:i') }}</div>
                <p class="whitespace-pre-line text-gray-800 dark:text-gray-200">{{ $ticket->content }}</p>
            </div>

            @foreach ($ticket->messages as $message)
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 {{ $message->user_id === $ticket->user_id ? '' : 'border-l-4 border-indigo-400' }}">
                    <div class="text-sm text-gray-500 mb-1">
                        {{ $message->user_id === $ticket->user_id ? $ticket->user->name : __('Qo\'llab-quvvatlash xizmati') }}
                        · {{ $message->created_at->format('d.m.Y H:i') }}
                    </div>
                    <p class="whitespace-pre-line text-gray-800 dark:text-gray-200">{{ $message->message }}</p>
                </div>
            @endforeach

            @if ($ticket->allowsMessages())
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                    <form method="POST" action="{{ route('admin.tickets.messages.store', $ticket) }}">
                        @csrf
                        <textarea name="message" rows="3" class="block w-full border-gray-300 rounded-md"
                                  placeholder="{{ __('Javob yozing...') }}" required>{{ old('message') }}</textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2"/>
                        <x-primary-button class="mt-3">{{ __('Javob yuborish') }}</x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('admin.tickets.close', $ticket) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="text-sm text-red-600">{{ __("Murojaatni yopish") }}</button>
                    </form>
                </div>
            @else
                <div class="text-sm text-gray-500">{{ __('Bu murojaat yopilgan.') }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
