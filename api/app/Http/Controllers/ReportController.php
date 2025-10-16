<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Perms;
use App\Repositories\ReportRepository; use App\Services\ReportService;

class ReportController {
    public static function daily(array $user): void {
        Perms::require($user,'REPORT_VIEW_DAILY'); $date=$_GET['date'] ?? date('Y-m-d'); $store=(int)($_GET['store_id'] ?? 1);
        $rep = new ReportRepository(); $vals = ReportService::calcDaily($date,$store);
        Response::json($vals);
    }
    public static function monthly(array $user): void {
        Perms::require($user,'REPORT_VIEW_MONTHLY'); $month=$_GET['month'] ?? date('Y-m'); $store=(int)($_GET['store_id'] ?? 1);
        $rep = new ReportRepository(); Response::json($rep->monthly($month,$store));
    }
}
