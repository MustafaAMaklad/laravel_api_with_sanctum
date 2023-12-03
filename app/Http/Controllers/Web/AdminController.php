<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Store;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function clientDashboard()
    {
        $clients = Client::with('user')->paginate(10);

        return view('admin.clients_dashboard', [
            'clients' => $clients,
        ]);
    }
    public function storeDashboard()
    {
        $stores = Store::with('user')->paginate(10);

        return view('admin.stores_dashboard', [
            'stores' => $stores,
        ]);
    }
}
