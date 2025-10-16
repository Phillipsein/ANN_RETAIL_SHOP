<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class CashRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function openSession(int $storeId,int $cashierId,int $opening): int {
        $s=$this->db->prepare("INSERT INTO cash_sessions (store_id,cashier_id,opened_at,opening_float) VALUES (?,?,NOW(),?)");
        $s->bind_param('iii',$storeId,$cashierId,$opening); if(!$s->execute()) return 0; return $s->insert_id;
    }
    public function closeSession(int $id,int $cash,int $momo): bool {
        $s=$this->db->prepare("UPDATE cash_sessions SET closed_at=NOW(), counted_cash=?, counted_momo=? WHERE id=?");
        $s->bind_param('iii',$cash,$momo,$id); return $s->execute();
    }
    public function addTx(array $d): int {
        $s=$this->db->prepare("INSERT INTO cash_transactions (store_id,cashier_id,session_id,tx_type,method,amount,reason,reference,tx_time) VALUES (?,?,?,?,?,?,?, ?, NOW())");
        $s->bind_param('iiississ',$d['store_id'],$d['cashier_id'],$d['session_id'],$d['tx_type'],$d['method'],$d['amount'],$d['reason'],$d['reference']);
        if(!$s->execute()) return 0; return $s->insert_id;
    }
}
