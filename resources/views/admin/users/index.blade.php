@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Foydalanuvchilar') }}</h2>
    </x-slot>
    {{ Breadcrumbs::render('admin.users.index') }}
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg p-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 rounded-lg p-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="GET" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-3">
                <input type="text" name="id" value="{{ request('id') }}" placeholder="{{ __('ID') }}"
                       class="border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md w-24">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="{{ __('Ism') }}"
                       class="border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md flex-1 min-w-[120px]">
                <input type="text" name="email" value="{{ request('email') }}" placeholder="{{ __('Email') }}"
                       class="border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md flex-1 min-w-[120px]">
                <select name="role" class="border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md">
                    <option value="">{{ __('Barcha rollar') }}</option>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" class="border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md">
                    <option value="">{{ __('Barcha statuslar') }}</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-primary-button>{{ __('Filtrlash') }}</x-primary-button>
            </form>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">{{ __('Ism') }}</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">{{ __('Rol') }}</th>
                        <th class="px-4 py-2">{{ __('Status') }}</th>
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
                            <td class="px-4 py-2">
                                @php
                                    $statusColors = [
                                        'active' => 'text-green-600',
                                        'wait' => 'text-amber-600',
                                        'suspended' => 'text-red-600',
                                        'deleted' => 'text-gray-400',
                                    ];
                                @endphp
                                <span class="{{ $statusColors[$user->status] ?? '' }}">
                                    {{ $statuses[$user->status] ?? $user->status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-indigo-600 text-sm">{{ __('Tahrirlash') }}</a>

                                @if (auth()->user()->isAdmin() && auth()->user()->isNot($user))
                                    @if ($user->isWaiting())
                                        <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 text-sm">{{ __('Faollashtirish') }}</button>
                                        </form>
                                    @endif
                                    @if ($user->isActive())
                                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline"
                                              onsubmit="return confirm('{{ __('Bloklansinmi?') }}');">
                                            @csrf
                                            <button type="submit" class="text-amber-600 text-sm">{{ __('Bloklash') }}</button>
                                        </form>
                                    @endif
                                    @if ($user->isSuspended())
                                        <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 text-sm">{{ __('Blokni olib tashlash') }}</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          class="inline" onsubmit="return confirm('{{ __("O'chirilsinmi?") }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 text-sm">{{ __("O'chirish") }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
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
