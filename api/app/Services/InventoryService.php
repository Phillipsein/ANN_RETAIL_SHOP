<?php
namespace App\Services;
use App\Repositories\InventoryRepository;
use App\Database\Connection;

class InventoryService {
    private InventoryRepository $inv;
    public function __construct(){ $this->inv = new InventoryRepository(); }

    public function postPurchase(int $purchaseId,int $storeId,array $items): void {
        $db = Connection::get(); $this->inv->begin();
        try {
            foreach ($items as $it) {
                $pid=$it['product_id']; $qty=(int)$it['qty']; $cost=(int)$it['unit_cost'];
                $this->inv->upsertBalance($storeId,$pid);
                $this->inv->adjustBalance($storeId,$pid,$qty);
                $this->inv->insertLedger($storeId,$pid,'PURCHASE',$purchaseId,$qty,0,$cost);
                $this->inv->updateMovingAvgCost($pid,$qty,$cost);
            }
            $this->inv->commit();
        } catch (\Throwable $e) { $this->inv->rollback(); throw $e; }
    }

    public function postSale(int $saleId,int $storeId,array $items): int {
        $db = Connection::get(); $this->inv->begin();
        $totalCOGS = 0;
        try {
            foreach ($items as $it) {
                $pid=$it['product_id']; $qty=(int)$it['qty']; $cost=(int)$it['cost_at_sale'];
                $this->inv->upsertBalance($storeId,$pid);
                $this->inv->adjustBalance($storeId,$pid,-$qty);
                $this->inv->insertLedger($storeId,$pid,'SALE',$saleId,0,$qty,$cost);
                $totalCOGS += $qty * $cost;
            }
            $this->inv->commit();
        } catch (\Throwable $e) { $this->inv->rollback(); throw $e; }
        return $totalCOGS;
    }

    public function postAdjustment(int $adjustId,int $storeId,int $productId,int $qtyDiff,int $unitCost): void {
        $this->inv->begin();
        try {
            $this->inv->upsertBalance($storeId,$productId);
            $this->inv->adjustBalance($storeId,$productId,$qtyDiff);
            $in = $qtyDiff>0 ? $qtyDiff : 0; $out = $qtyDiff<0 ? -$qtyDiff : 0;
            $this->inv->insertLedger($storeId,$productId,'ADJUST',$adjustId,$in,$out,$unitCost);
            $this->inv->commit();
        } catch (\Throwable $e) { $this->inv->rollback(); throw $e; }
    }
}
