@php
    // Access the record and fetch reminders dynamically
    $record = $getRecord();
    $contacts = $record ? $record->contacts : collect();
@endphp

@if ($contacts->isEmpty())
    <p>No contacts found.</p>
@else
    @foreach ($contacts as $contact)
        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 mb-2">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 self-start">
                    <img class="w-8 h-8 rounded-full" src="/docs/images/people/profile-picture-1.jpg" alt="Neil image">
                </div>
                <div class="flex-1 min-w-0 ms-4">
                    <div class="flex items-center justify-between">
                        <!-- Link -->
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                {{ $contact->name }}
                            </p>
                        </div>

                        <!-- Dropdown -->
                        <div>
                            <x-filament::dropdown placement="top-start">
                                <x-slot name="trigger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                    </svg>
                                </x-slot>

                                <x-filament::dropdown.list>
{{--                                    <x-filament::dropdown.list.item tag="a" href="#">--}}
{{--                                        <p class="text-red-600 !important">View contact</p>--}}
{{--                                    </x-filament::dropdown.list.item>--}}
                                    <hr>
                                    <x-filament::dropdown.list.item
                                        color="danger"
                                        wire:click="detachContact({{ $contact->id }})"
                                    >
                                        <p class="text-red-600">Remove contact from this item</p>
                                    </x-filament::dropdown.list.item>
                                </x-filament::dropdown.list>
                            </x-filament::dropdown>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                        {{ $contact->email }}
                    </p>
                </div>
            </div>
        </div>
    @endforeach
@endif


