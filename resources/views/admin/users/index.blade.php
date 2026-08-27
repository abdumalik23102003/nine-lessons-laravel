@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Foydalanuvchilar') }}</h2>
    </x-slot>
    {{ Breadcrumbs::render('admin.users.index') }}
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300">{{ session('status') }}</div>
            @endif

            <form method="GET" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-3">
                <input type="text" name="id" value="{{ request('id') }}" placeholder="{{ __('ID') }}"
                       class="border-gray-300 rounded-md w-24">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="{{ __('Ism') }}"
                       class="border-gray-300 rounded-md flex-1 min-w-[150px]">
                <input type="text" name="email" value="{{ request('email') }}" placeholder="{{ __('Email') }}"
                       class="border-gray-300 rounded-md flex-1 min-w-[150px]">
                <select name="role" class="border-gray-300 rounded-md">
                    <option value="">{{ __('Barcha rollar') }}</option>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-primary-button>{{ __('Filtrlash') }}</x-primary-button>
            </form>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">{{ __('Ism') }}</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">{{ __('Rol') }}</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-2 text-gray-500">{{ $user->id }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $user->email }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $roles[$user->role] ?? $user->role }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-indigo-600 text-sm">{{ __('Tahrirlash') }}</a>
                                @if (auth()->user()->isAdmin() && auth()->user()->isNot($user))
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          class="inline" onsubmit="return confirm('{{ __("O'chirilsinmi?") }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 text-sm ml-3">{{ __("O'chirish") }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-4 py-6 text-center text-gray-500">{{ __('Foydalanuvchilar topilmadi.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
