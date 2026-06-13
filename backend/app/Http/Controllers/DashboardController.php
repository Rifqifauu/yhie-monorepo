<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\ProgramRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        //ambil program registration dengan type program
        $registrations = ProgramRegistration::where()->get();


    }
}
