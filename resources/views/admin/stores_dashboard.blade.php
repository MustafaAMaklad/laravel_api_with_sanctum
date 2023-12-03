{{-- resources\views\admin\stores_dashboard.blade.php --}}

@extends('layout.dashboard')

@section('title', 'Dashboard')

@section('table-title', 'Stores')

@section('content')
    @foreach ($stores as $store)
        <tr>
            <td>{{ $store->id }}</td>
            <td>{{ $store->name }}</td>
            <td>{{ $store['user']->email }}</td>
            <td>
                @switch($store['user']->status)
                    @case('blocked')
                        <span class="blocked">{{ $store['user']->status }}</span>
                    @break

                    @case('active')
                        <span class="active">{{ $store['user']->status }}</span>
                    @break

                    @default
                        <span class="pending">{{ $store['user']->status }}</span>
                @endswitch
            </td>

        </tr>
    @endforeach
@endsection

@section('pagination-links')
    {{ $stores->links() }}
@endsection
