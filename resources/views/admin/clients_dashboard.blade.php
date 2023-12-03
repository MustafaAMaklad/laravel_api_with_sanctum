<!-- resources\views\admin\clients_dashboard.blade.php -->

@extends('layout.dashboard')

@section('title', 'Dashboard')

@section('table-title', 'Clients')

@section('table-headers')
    <th>User ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Status</th>
@endsection

@section('content')
    @foreach ($clients as $client)
        <tr>
            <td>{{ $client->id }} </td>
            <td>{{ $client->full_name }}</td>
            <td>{{ $client['user']->email }}</td>
            <td>
                @switch($client['user']->status)
                    @case('blocked')
                        <span class="blocked">{{ $client['user']->status }}</span>
                    @break

                    @default
                        <span class="active">{{ $client['user']->status }}</span>
                @endswitch
            </td>
        </tr>
    @endforeach
@endsection

@section('pagination-links')
    {{ $clients->links() }}
@endsection
