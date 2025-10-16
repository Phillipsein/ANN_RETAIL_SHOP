<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class ReportRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function daily(string $date,int $storeId): array {
        $s=$this->db->prepare("SELECT total_sales,total_cogs,gross_margin,sales_cash,sales_momo,sales_other FROM report_daily_metrics WHERE report_date=? AND store_id=?");
        $s->bind_param('si',$date,$storeId); $s->execute(); $r=$s->get_result(); $row=$r->fetch_assoc(); return $row ?: ['total_sales'=>0,'total_cogs'=>0,'gross_margin'=>0,'sales_cash'=>0,'sales_momo'=>0,'sales_other'=>0];
    }
    public function monthly(string $month,int $storeId): array {
        $s=$this->db->prepare("SELECT total_sales,total_cogs,gross_margin,sales_cash,sales_momo,sales_other FROM report_monthly_metrics WHERE period_month=? AND store_id=?");
        $s->bind_param('si',$month,$storeId); $s->execute(); $r=$s->get_result(); $row=$r->fetch_assoc(); return $row ?: ['total_sales'=>0,'total_cogs'=>0,'gross_margin'=>0,'sales_cash'=>0,'sales_momo'=>0,'sales_other'=>0];
    }
    public function upsertDaily(string $date,int $storeId,array $vals): void {
        $s=$this->db->prepare("INSERT INTO report_daily_metrics (report_date,store_id,total_sales,total_cogs,gross_margin,sales_cash,sales_momo,sales_other) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE total_sales=VALUES(total_sales), total_cogs=VALUES(total_cogs), gross_margin=VALUES(gross_margin), sales_cash=VALUES(sales_cash), sales_momo=VALUES(sales_momo), sales_other=VALUES(sales_other)");
        $s->bind_param('si iiiiii', $date,$storeId,$vals['total_sales'],$vals['total_cogs'],$vals['gross_margin'],$vals['sales_cash'],$vals['sales_momo'],$vals['sales_other']);
    }
}
