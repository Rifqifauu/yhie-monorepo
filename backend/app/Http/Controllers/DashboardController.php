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

            // Ambil statistik program registration secara efisien dalam satu query
            $registrationStats = ProgramRegistration::selectRaw("
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
            ")->first();

            $pendingRegistrations = (int) ($registrationStats->pending ?? 0);
            $approvedRegistration = (int) ($registrationStats->approved ?? 0);
            $rejectedRegistration = (int) ($registrationStats->rejected ?? 0);

            $articles = Article::where('is_published', 'true')->count();

            // Ambil statistik transaksi secara efisien dalam satu query
            $transactionStats = Transaction::selectRaw("
                COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as revenue
            ")->first();

            $pendingTransaction = (int) ($transactionStats->pending ?? 0);
            $totalRevenue = (float) ($transactionStats->revenue ?? 0);

            $upcomingSchedule = Schedule::where('start_date', '<', now()->addDays(7))->get();
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
