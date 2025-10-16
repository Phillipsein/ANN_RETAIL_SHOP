<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class InventoryRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function begin(): void { $this->db->begin_transaction(); }
    public function commit(): void { $this->db->commit(); }
    public function rollback(): void { $this->db->rollback(); }

    public function upsertBalance(int $storeId, int $productId): void {
        $this->db->query("INSERT IGNORE INTO product_stock_balances (store_id,product_id,qty_on_hand) VALUES ($storeId,$productId,0)");
    }

    public function adjustBalance(int $storeId, int $productId, int $qtyDiff): void {
        $this->db->query("UPDATE product_stock_balances SET qty_on_hand = qty_on_hand + ($qtyDiff) WHERE store_id=$storeId AND product_id=$productId");
    }

    public function insertLedger(int $storeId,int $productId,string $sourceType,int $sourceId,int $qtyIn,int $qtyOut,int $unitCost): void {
        $s=$this->db->prepare("INSERT INTO stock_ledger (store_id,product_id,source_type,source_id,qty_in,qty_out,unit_cost) VALUES (?,?,?,?,?,?,?)");
        $s->bind_param('iisiisi',$storeId,$productId,$sourceType,$sourceId,$qtyIn,$qtyOut,$unitCost); $s->execute();
    }

    public function updateMovingAvgCost(int $productId, int $qtyIn, int $unitCost): void {
        // moving average: new_avg = (old_avg*old_qty + unitCost*qtyIn) / (old_qty+qtyIn)
        // approximate old_qty from balances summed across stores (basic approach)
        $res = $this->db->query("SELECT SUM(qty_on_hand) AS q FROM product_stock_balances WHERE product_id=$productId");
        $row = $res->fetch_assoc(); $oldQty = (int)($row['q'] ?? 0);
        $res2 = $this->db->query("SELECT moving_avg_cost FROM products WHERE id=$productId");
        $row2 = $res2->fetch_assoc(); $oldAvg = (int)($row2['moving_avg_cost'] ?? 0);
        $newQty = $oldQty + $qtyIn;
        $newAvg = $newQty > 0 ? (int) floor( ($oldAvg * $oldQty + $unitCost * $qtyIn) / $newQty ) : $oldAvg;
        $this->db->query("UPDATE products SET moving_avg_cost=$newAvg WHERE id=$productId");
    }
}
