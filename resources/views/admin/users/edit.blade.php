@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Foydalanuvchini tahrirlash') }}</h2>
    </x-slot>
    {{ Breadcrumbs::render('admin.users.edit', $user) }}
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" value="Ism"/>
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      value="{{ old('name', $user->name) }}" required autofocus/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" value="Email"/>
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                      value="{{ old('email', $user->email) }}" required/>
                        <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="role" value="Rol"/>
                        @if ($canChangeRole)
                            <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md">
                                @foreach ($roles as $value => $label)
                                    <option
                                        value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2"/>
                        @else
                            <div class="mt-1 text-sm text-gray-500">
                                {{ $roles[$user->role] ?? $user->role }}
                                <span class="text-xs">({{ __("faqat admin, va faqat boshqa foydalanuvchining rolini o'zgartira oladi") }})</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ __('Saqlash') }}</x-primary-button>
                        <a href="{{ route('admin.users.index') }}"
                           class="text-sm text-gray-600 dark:text-gray-400">{{ __('Bekor qilish') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
