<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class PurchaseRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function createPurchase(array $h, array $items): int {
        $s=$this->db->prepare("INSERT INTO purchases (store_id,supplier_id,invoice_no,invoice_date,transport_cost,other_cost,total_cost,created_by) VALUES (?,?,?,?,?,?,?,?)");
        $s->bind_param('iissiiii', $h['store_id'],$h['supplier_id'],$h['invoice_no'],$h['invoice_date'],$h['transport_cost'],$h['other_cost'],$h['total_cost'],$h['created_by']);
        if(!$s->execute()) return 0; $pid=$s->insert_id;
        $si=$this->db->prepare("INSERT INTO purchase_items (purchase_id,product_id,qty,unit_cost) VALUES (?,?,?,?)");
        foreach ($items as $it) { $si->bind_param('iiii',$pid,$it['product_id'],$it['qty'],$it['unit_cost']); $si->execute(); }
        return $pid;
    }

    public function list(array $filter): array {
        $q = "SELECT p.id,p.supplier_id,p.invoice_no,p.invoice_date,p.total_cost,p.created_at,s.name AS supplier FROM purchases p JOIN suppliers s ON s.id=p.supplier_id WHERE 1=1";
        if (!empty($filter['from'])) $q .= " AND p.invoice_date >= '" . $this->db->real_escape_string($filter['from']) . "'";
        if (!empty($filter['to'])) $q .= " AND p.invoice_date <= '" . $this->db->real_escape_string($filter['to']) . "'";
        $q .= " ORDER BY p.id DESC LIMIT 200";
        $r=$this->db->query($q); $o=[]; while($x=$r->fetch_assoc()) $o[]=$x; return $o;
    }

    public function find(int $id): array {
        $h=$this->db->query("SELECT * FROM purchases WHERE id=$id")->fetch_assoc() ?: [];
        $it=[]; $r=$this->db->query("SELECT * FROM purchase_items WHERE purchase_id=$id");
        while($x=$r->fetch_assoc()) $it[]=$x;
        return ['header'=>$h,'items'=>$it];
    }
}
