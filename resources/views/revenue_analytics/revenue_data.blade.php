<!-- resources/views/revenue-analytics/revenue_data.blade.php -->

@extends('template.conf')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Revenue Data</h1>
    @include('atoms.filter')

    <div class="overflow-x-auto shadow rounded-lg">
        <table class="min-w-full border-collapse table-auto">
            <thead>
                <tr class="bg-dark-blue text-white text-xs uppercase tracking-wider">
                    <th class="px-4 py-3 border border-gray-200 text-left">Regional</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Witel</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">LCCD</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Stream</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Product Name</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">GL Account</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">BP Number</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Customer Name</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Customer Type</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Target</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Revenue</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Periode</th>
                    <th class="px-4 py-3 border border-gray-200 text-left">Target RKAPP</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse ($tableData as $row)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 border border-gray-200">{{ $row->regional }}</td>
                        <td class="px-4 py-3 border border-gray-200">{{ $row->witel ?? '–' }}</td>
                        <td class="px-4 py-3 border border-gray-200">{{ $row->lccd }}</td>
                        <td class="px-4 py-3 border border-gray-200">{{ $row->stream }}</td>
                        <td class="px-4 py-3 border border-gray-200">{{ $row->product_name }}</td>
                        <td class="px-4 py-3 border border-gray-200">{{ $row->gl_account }}</td>
                        <td class="px-4 py-3 border border-gray-200">{{ $row->bp_number }}</td>
                        <td class="px-4 py-3 border border-gray-200">{{ $row->customer_name }}</td>
                        <td class="px-4 py-3 border border-gray-200">{{ $row->customer_type }}</td>
                        <td class="px-4 py-3 border border-gray-200 text-right">
                            Rp {{ number_format($row->target, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 border border-gray-200 text-right">
                            Rp {{ number_format($row->revenue, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 border border-gray-200">
                            {{ substr($row->periode, 0, 4) }}-{{ substr($row->periode, 4, 2) }}
                        </td>
                        <td class="px-4 py-3 border border-gray-200 text-right">
                            Rp {{ number_format($row->target_rkapp, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="px-4 py-6 text-center text-gray-500">
                            Tidak ada data ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 ">
        {{ $tableData->links() }}
    </div>
@endsection
