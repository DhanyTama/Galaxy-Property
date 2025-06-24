<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Pembayaran Gaji') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('salary-requests.store') }}" id="salaryForm">
                        @csrf

                        <div>
                            <x-input-label for="base_salary" :value="__('Gaji Pokok')" />
                            <x-text-input id="base_salary" class="block mt-1 w-full" type="number" name="base_salary"
                                :value="old('base_salary')" required autofocus oninput="calculateSalary()" />
                            <x-input-error :messages="$errors->get('base_salary')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="bonus" :value="__('Bonus')" />
                            <x-text-input id="bonus" class="block mt-1 w-full" type="number" name="bonus"
                                :value="old('bonus', 0)" oninput="calculateSalary()" />
                            <x-input-error :messages="$errors->get('bonus')" class="mt-2" />
                        </div>

                        <div class="mt-6 p-4 bg-gray-100 rounded-md">
                            <h3 class="font-semibold text-lg mb-2">{{ __('Detail Perhitungan') }}</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Gaji Kotor (Gaji Pokok + Bonus):') }}</p>
                                    <p class="font-bold text-gray-800" id="gross_salary_display">Rp 0</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Persentase PPh 21:') }}</p>
                                    <p class="font-bold text-gray-800" id="pph_percentage_display">0%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Jumlah PPh 21:') }}</p>
                                    <p class="font-bold text-gray-800" id="pph_amount_display">Rp 0</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Gaji Bersih (Gaji Kotor - PPh 21):') }}</p>
                                    <p class="font-bold text-gray-800" id="net_salary_display">Rp 0</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Ajukan Pembayaran') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.formatRupiah = function(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            };

            window.calculateSalary = function() {
                const baseSalaryInput = document.getElementById('base_salary');
                const bonusInput = document.getElementById('bonus');

                if (!baseSalaryInput || !bonusInput) {
                    console.error('One or both input elements not found!');
                    return;
                }

                const baseSalary = parseFloat(baseSalaryInput.value) || 0;
                const bonus = parseFloat(bonusInput.value) || 0;

                const grossSalary = baseSalary + bonus;
                let pphPercentage = 0;

                if (grossSalary <= 5000000) {
                    pphPercentage = 5;
                } else if (grossSalary > 5000000 && grossSalary <= 20000000) {
                    pphPercentage = 10;
                } else {
                    pphPercentage = 15;
                }

                const pphAmount = grossSalary * (pphPercentage / 100);
                const netSalary = grossSalary - pphAmount;

                document.getElementById('gross_salary_display').textContent = window.formatRupiah(grossSalary);
                document.getElementById('pph_percentage_display').textContent = pphPercentage + '%';
                document.getElementById('pph_amount_display').textContent = window.formatRupiah(pphAmount);
                document.getElementById('net_salary_display').textContent = window.formatRupiah(netSalary);
            };

            document.addEventListener('DOMContentLoaded', function() {
                window.calculateSalary();
            });
        </script>
    @endpush
</x-app-layout>
