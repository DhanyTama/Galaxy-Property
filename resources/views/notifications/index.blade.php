<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifikasi Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">{{ __('Notifikasi Terbaru') }}</h3>

                    @if ($notifications->isEmpty())
                        <p class="text-gray-600">{{ __('Tidak ada notifikasi baru.') }}</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($notifications as $notification)
                                <div
                                    class="p-4 border rounded-md {{ $notification->read_at ? 'bg-gray-50 border-gray-200' : 'bg-blue-50 border-blue-200 font-semibold' }}">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm">
                                            @if ($notification->type === 'App\\Notifications\\SalaryRequestStatusUpdated')
                                                @php
                                                    $data = $notification->data;
                                                @endphp
                                                <span class="text-gray-800">{{ $data['message'] }}</span>
                                                <br>
                                                <span class="text-xs text-gray-500">
                                                    (Dari {{ $data['approved_by'] }} pada
                                                    {{ $notification->created_at->format('d M Y H:i') }})
                                                </span>
                                            @elseif ($notification->type === 'App\\Notifications\\SalaryPaymentCompleted')
                                                @php
                                                    $data = $notification->data;
                                                @endphp
                                                <span class="text-gray-800">{{ $data['message'] }}</span>
                                            @else
                                                <span
                                                    class="text-gray-800">{{ $notification->data['message'] ?? 'Notifikasi baru.' }}</span>
                                                <span
                                                    class="text-xs text-gray-500">{{ $notification->created_at->format('d M Y H:i') }}</span>
                                            @endif
                                        </p>
                                        @if (!$notification->read_at)
                                            <span
                                                class="text-blue-600 text-xs px-2 py-1 rounded-full bg-blue-100">Baru</span>
                                        @endif
                                    </div>
                                    @if (isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}"
                                            class="text-indigo-600 hover:text-indigo-900 text-sm mt-2 block">
                                            Lihat Detail
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
