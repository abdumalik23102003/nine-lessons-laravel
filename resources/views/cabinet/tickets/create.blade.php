<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Yangi murojaat') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('cabinet.tickets.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="subject" value="Mavzu" />
                        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" value="{{ old('subject') }}" required autofocus />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="content" value="Xabar" />
                        <textarea id="content" name="content" rows="6" class="mt-1 block w-full border-gray-300 rounded-md" required>{{ old('content') }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <x-primary-button>{{ __('Yuborish') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
