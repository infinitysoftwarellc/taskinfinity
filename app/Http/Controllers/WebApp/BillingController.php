<?php

namespace App\Http\Controllers\WebApp;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(): View
    {
        return view('webapp.billing.index');
    }

    public function stripe(): View
    {
        return view('webapp.billing.stripe');
    }

    public function mercadoPago(): View
    {
        return view('webapp.billing.mercado-pago');
    }
}
