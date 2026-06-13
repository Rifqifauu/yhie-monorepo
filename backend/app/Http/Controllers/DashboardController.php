<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use App\Models\Transaction;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            //ambil program registration dengan type program
            $registrations = ProgramRegistration::where(
                "status",
                "pending",
            )->count();
            $articles = Article::where("is_published", "true")->count();
            $pendingTransaction = Transaction::where(
                "payment_status",
                "pending",
            )->count();
            $successTransaction = Transaction::where(
                "payment_status",
                "success",
            )->sum("amount");
            $upcomingSchedule = Schedule::where(
                "start_date",
                "<",
                now()->addDays(7),
            )->get();
        } catch (\Exception $e) {
            return response()->json(
                [
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
        return response()->json([
            'pendingRegistrations' => $pendingRegistrations,
            'approvedRegistration' => $approvedRegistration,
            'rejectedRegistration' => $rejectedRegistration,
            'articles' => $articles,
            'pendingTransaction' => $pendingTransaction,
            'totalRevenue' => $totalRevenue,
            'upcomingSchedule' => $upcomingSchedule,
        ]);
    }
}
