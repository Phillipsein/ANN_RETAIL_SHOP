<?php

namespace App\Services;

use App\Database\Connection;

class ReportService
{
    // Quick calculators for daily/monthly from sales table (approximate)
    public static function calcDaily(string $date, int $storeId): array
    {
        $db = Connection::get();
        $dateStart = $db->real_escape_string($date) . ' 00:00:00';
        $dateEnd   = $db->real_escape_string($date) . ' 23:59:59';
        $q = "SELECT
                SUM(si.qty*si.unit_price - si.discount) AS total_sales,
                SUM(si.qty*si.cost_at_sale) AS total_cogs,
                SUM(CASE WHEN s.pay_method='CASH' THEN (si.qty*si.unit_price - si.discount) ELSE 0 END) AS sales_cash,
                SUM(CASE WHEN s.pay_method='MOMO' THEN (si.qty*si.unit_price - si.discount) ELSE 0 END) AS sales_momo,
                SUM(CASE WHEN s.pay_method NOT IN ('CASH','MOMO') THEN (si.qty*si.unit_price - si.discount) ELSE 0 END) AS sales_other
              FROM sales s JOIN sale_items si ON si.sale_id=s.id
              WHERE s.store_id=$storeId AND s.sale_time BETWEEN '$dateStart' AND '$dateEnd'";
        $r = $db->query($q);
        $row = $r->fetch_assoc() ?: ['total_sales' => 0, 'total_cogs' => 0, 'sales_cash' => 0, 'sales_momo' => 0, 'sales_other' => 0];
        $row['total_sales'] = (int)$row['total_sales'];
        $row['total_cogs']  = (int)$row['total_cogs'];
        $row['gross_margin'] = $row['total_sales'] - $row['total_cogs'];
        $row['sales_cash']  = (int)$row['sales_cash'];
        $row['sales_momo']  = (int)$row['sales_momo'];
        $row['sales_other'] = (int)$row['sales_other'];
        return $row;
    }
}
