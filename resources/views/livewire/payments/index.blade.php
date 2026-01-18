<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Facturación y Pagos</h2>

            @can('create', App\Models\Payment::class)
                <a href="{{ route('payments.create') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                    + Registrar Pago
                </a>
            @endcan
        </div>

        {{-- Totales del período --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-50 rounded-lg shadow p-4 border-l-4 border-green-500">
                <p class="text-sm text-green-800 font-medium">Ingresos Completados</p>
                <p class="text-3xl font-bold text-green-900">${{ number_format($totals['total_amount'], 2) }}</p>
                <p class="text-xs text-green-600 mt-1">{{ $totals['total_count'] }} pagos</p>
            </div>

            <div class="bg-yellow-50 rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <p class="text-sm text-yellow-800 font-medium">Pagos Pendientes</p>
                <p class="text-3xl font-bold text-yellow-900">${{ number_format($totals['pending_amount'], 2) }}</p>
                <p class="text-xs text-yellow-600 mt-1">{{ $totals['pending_count'] }} pagos</p>
            </div>

            <div class="bg-blue-50 rounded-lg shadow p-4 border-l-4 border-blue-500">
                <p class="text-sm text-blue-800 font-medium">Promedio por Pago</p>
                <p class="text-3xl font-bold text-blue-900">
                    ${{ $totals['total_count'] > 0 ? number_format($totals['total_amount'] / $totals['total_count'], 2) : '0.00' }}
                </p>
                <p class="text-xs text-blue-600 mt-1">Del período</p>
            </div>

            <div class="bg-purple-50 rounded-lg shadow p-4 border-l-4 border-purple-500">
                <p class="text-sm text-purple-800 font-medium">Total General</p>
                <p class="text-3xl font-bold text-purple-900">
                    ${{ number_format($totals['total_amount'] + $totals['pending_amount'], 2) }}
                </p>
                <p class="text-xs text-purple-600 mt-1">Incluye pendientes</p>
            </div>
        </div>

        {{-- Selector de período --}}
        <div class="bg-white p-4 rounded-lg shadow mb-4">
            <div class="flex gap-2 mb-4">
                <button wire:click="setPeriod('today')"
                    class="px-3 py-1 rounded {{ $period === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Hoy
                </button>
                <button wire:click="setPeriod('week')"
                    class="px-3 py-1 rounded {{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Esta Semana
                </button>
                <button wire:click="setPeriod('month')"
                    class="px-3 py-1 rounded {{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Este Mes
                </button>
                <button wire:click="setPeriod('year')"
                    class="px-3 py-1 rounded {{ $period === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Este Año
                </button>
                <button wire:click="setPeriod('custom')"
                    class="px-3 py-1 rounded {{ $period === 'custom' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Personalizado
                </button>
            </div>

            {{-- Filtros --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" wire:model.live="filterDateFrom"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" wire:model.live="filterDateTo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select wire:model.live="filterStatus"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos</option>
                        @foreach ($paymentStatuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Método</label>
                    <select wire:model.live="filterMethod"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos</option>
                        @foreach ($paymentMethods as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Paciente o #pago..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes flash --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Tabla de pagos --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                # Pago
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paciente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Concepto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Método
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $payment->payment_number }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $payment->creator->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payment->payment_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('patients.show', $payment->patient) }}"
                                        class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                        {{ $payment->patient->full_name }}
                                    </a>
                                    <div class="text-xs text-gray-500">
                                        {{ $payment->patient->identification_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $payment->concept }}</div>
                                    @if ($payment->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($payment->description, 40) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-{{ $payment->payment_method->color() }}-100 text-{{ $payment->payment_method->color() }}-800">
                                        {{ $payment->payment_method->icon() }} {{ $payment->payment_method->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ $payment->formatted_amount }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-{{ $payment->status->color() }}-100 text-{{ $payment->status->color() }}-800">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        @can('view', $payment)
                                            <a href="{{ route('payments.receipt.download', $payment) }}" target="_blank"
                                                class="text-blue-600 hover:text-blue-900">
                                                PDF
                                            </a>
                                        @endcan

                                        @can('cancel', $payment)
                                            @if ($payment->canBeCancelled())
                                                <button wire:click="cancelPayment({{ $payment->id }})"
                                                    wire:confirm="¿Cancelar este pago?"
                                                    class="text-red-600 hover:text-red-900">
                                                    Cancelar
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payments->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <p class="text-gray-500">No hay pagos registrados para los filtros seleccionados</p>
            </div>
        @endif
    </div>
</div>
