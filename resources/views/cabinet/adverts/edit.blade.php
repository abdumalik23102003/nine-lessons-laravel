@php use App\Models\Advert;use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __("E'lonni tahrirlash") }}</h2>
    </x-slot>

    {{ Breadcrumbs::render('cabinet.adverts.edit', $advert) }}

    <div class="py-12" x-data="{ tab: 'main' }">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                <nav class="-mb-px flex space-x-6">
                    <button type="button" @click="tab = 'main'"
                            :class="tab === 'main' ? 'border-gray-800 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500'"
                            class="border-b-2 py-3 px-1 text-sm font-medium">
                        {{ __('Asosiy') }}
                    </button>
                    <button type="button" @click="tab = 'photos'"
                            :class="tab === 'photos' ? 'border-gray-800 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500'"
                            class="border-b-2 py-3 px-1 text-sm font-medium">
                        {{ __('Rasmlar') }}
                    </button>
                    @if ($advert->category->allAttributes())
                        <button type="button" @click="tab = 'attributes'"
                                :class="tab === 'attributes' ? 'border-gray-800 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500'"
                                class="border-b-2 py-3 px-1 text-sm font-medium">
                            {{ __('Xususiyatlar') }}
                        </button>
                    @endif
                </nav>
            </div>

            <div x-show="tab === 'main'" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('cabinet.adverts.update', $advert) }}">
                    @method('PUT')
                    @include('cabinet.adverts._form')
                </form>

                {{-- edit.blade.php, "Asosiy" tab div ichida, </form>dan keyin --}}
                @if ($advert->reject_reason)
                    <div
                        class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3 text-sm text-red-700 dark:text-red-300">
                        <strong>{{ __('Rad etildi') }}:</strong> {{ $advert->reject_reason }}
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                        {{ __('Status') }}: {{ Advert::statusesList()[$advert->status] }}
                    </span>

                    @if ($advert->isDraft())
                        <form method="POST" action="{{ route('cabinet.adverts.send-to-moderation', $advert) }}">
                            @csrf
                            <x-primary-button>{{ __('Moderatsiyaga yuborish') }}</x-primary-button>
                        </form>
                    @endif
                </div>
            </div>

            <div x-show="tab === 'photos'" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                    @foreach ($advert->photos as $photo)
                        <div class="relative group">
                            <img src="{{ $photo->getFileUrl() }}" class="rounded-md w-full h-24 object-cover" alt="rasm">

                            <form method="POST"
                                  action="{{ route('cabinet.adverts.photos.destroy', [$advert, $photo]) }}"
                                  class="absolute top-1 right-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow transition-colors"
                                        title="{{ __('O\'chirish') }}">
                                    &times;
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('cabinet.adverts.photos.store', $advert) }}" enctype="multipart/form-data" class="mt-4 border-t pt-4">
                    @csrf
                    <div>
                        <x-input-label for="photo_file" :value="__('Yangi rasm tanlang')" class="mb-2" />
                        <input id="photo_file" type="file" name="file" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-300">
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <x-primary-button class="mt-4">{{ __('Yuklash') }}</x-primary-button>
                </form>
            </div>

            @if ($advert->category->allAttributes())
                <div x-show="tab === 'attributes'" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <form method="POST" action="{{ route('cabinet.adverts.attributes.update', $advert) }}">
                        @csrf
                        @method('PUT')

                        @foreach ($advert->category->allAttributes() as $attribute)
                            <div class="mb-4">
                                <x-input-label :value="$attribute->name"/>

                                @if ($attribute->isSelect())
                                    <select name="attributes[{{ $attribute->id }}]"
                                            class="mt-1 block w-full border-gray-300 rounded-md">
                                        <option value=""></option>
                                        @foreach ($attribute->variants as $variant)
                                            <option
                                                value="{{ $variant }}" @selected(old('attributes.' . $attribute->id, $advert->getValue($attribute->id)) == $variant)>
                                                {{ $variant }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <x-text-input
                                        name="attributes[{{ $attribute->id }}]"
                                        type="{{ $attribute->isNumber() ? 'number' : 'text' }}"
                                        class="mt-1 block w-full"
                                        value="{{ old('attributes.' . $attribute->id, $advert->getValue($attribute->id)) }}"
                                    />
                                @endif

                                <x-input-error :messages="$errors->get('attributes.' . $attribute->id)" class="mt-2"/>
                            </div>
                        @endforeach

                        <x-primary-button>{{ __('Saqlash') }}</x-primary-button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
