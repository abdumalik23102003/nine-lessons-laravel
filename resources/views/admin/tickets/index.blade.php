<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Murojaatlar') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <form method="GET" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-3">
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">{{ __('Barcha statuslar') }}</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-primary-button>{{ __('Filtrlash') }}</x-primary-button>
            </form>

            @forelse ($tickets as $ticket)
                <a href="{{ route('admin.tickets.show', $ticket) }}" class="block bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $ticket->subject }}</div>
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                            {{ $statuses[$ticket->status] ?? $ticket->status }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">{{ $ticket->user->name }} · {{ $ticket->created_at->format('d.m.Y H:i') }}</div>
                </a>
            @empty
                <div class="text-gray-500">{{ __('Murojaatlar yo\'q.') }}</div>
            @endforelse

            <div>{{ $tickets->links() }}</div>
        </div>
    </div>
</x-app-layout>
