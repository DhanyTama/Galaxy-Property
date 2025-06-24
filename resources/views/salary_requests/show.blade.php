<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Permintaan Gaji') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">
                        {{ __('Detail Permintaan Gaji #') }}{{ $salaryRequest->id }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="font-semibold text-gray-700">Diajukan Oleh:</p>
                            <p>{{ $salaryRequest->user->name }} ({{ $salaryRequest->user->email }})</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Tanggal Pengajuan:</p>
                            <p>{{ $salaryRequest->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Gaji Pokok:</p>
                            <p>Rp {{ number_format($salaryRequest->base_salary, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Bonus:</p>
                            <p>Rp {{ number_format($salaryRequest->bonus, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Gaji Kotor:</p>
                            <p>Rp {{ number_format($salaryRequest->base_salary + $salaryRequest->bonus, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Persentase PPh 21:</p>
                            <p>{{ $salaryRequest->pph_percentage }}%</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Jumlah PPh 21:</p>
                            <p>Rp {{ number_format($salaryRequest->pph_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Gaji Bersih:</p>
                            <p class="font-bold text-lg text-green-700">Rp
                                {{ number_format($salaryRequest->net_salary, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <h4 class="font-semibold text-md mb-3">{{ __('Status & Riwayat') }}</h4>
                    <div class="space-y-3">
                        <div>
                            <p class="font-semibold text-gray-700">Status:</p>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if ($salaryRequest->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($salaryRequest->status === 'approved') bg-blue-100 text-blue-800
                                @elseif($salaryRequest->status === 'rejected') bg-red-100 text-red-800
                                @elseif($salaryRequest->status === 'paid') bg-green-100 text-green-800 @endif">
                                {{ ucfirst($salaryRequest->status) }}
                            </span>
                        </div>

                        @if ($salaryRequest->approved_by)
                            <div>
                                <p class="font-semibold text-gray-700">Disetujui Oleh:</p>
                                <p>{{ $salaryRequest->approvedBy->name ?? 'N/A' }} pada
                                    {{ $salaryRequest->approved_at->format('d M Y H:i') }}</p>
                            </div>
                        @endif

                        @if ($salaryRequest->rejection_reason)
                            <div>
                                <p class="font-semibold text-gray-700">Alasan Penolakan:</p>
                                <p class="text-red-600 italic">{{ $salaryRequest->rejection_reason }}</p>
                            </div>
                        @endif

                        @if ($salaryRequest->processed_by)
                            <div>
                                <p class="font-semibold text-gray-700">Diproses Pembayaran Oleh:</p>
                                <p>{{ $salaryRequest->processedBy->name ?? 'N/A' }} pada
                                    {{ $salaryRequest->processed_at->format('d M Y H:i') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button onclick="window.history.back()">
                            {{ __('Kembali') }}
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
