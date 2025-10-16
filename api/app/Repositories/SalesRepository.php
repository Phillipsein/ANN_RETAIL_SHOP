<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class SalesRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function createSale(array $h, array $items): array {
        $s=$this->db->prepare("INSERT INTO sales (store_id,cashier_id,sale_no,sale_time,pay_method,customer_id,paid_amount,discount_total,notes) VALUES (?,?,?,?,?,?,?,?,?)");
        $s->bind_param('issssiiis', $h['store_id'],$h['cashier_id'],$h['sale_no'],$h['sale_time'],$h['pay_method'],$h['customer_id'],$h['paid_amount'],$h['discount_total'],$h['notes']);
        if(!$s->execute()) return [0,0];
        $sid=$s->insert_id; $total=0;
        $si=$this->db->prepare("INSERT INTO sale_items (sale_id,product_id,qty,unit_price,discount,cost_at_sale) VALUES (?,?,?,?,?,?)");
        foreach ($items as $it) {
            $line = $it['qty'] * $it['unit_price'] - $it['discount'];
            $total += $line;
            $si->bind_param('iiiiii',$sid,$it['product_id'],$it['qty'],$it['unit_price'],$it['discount'],$it['cost_at_sale']);
            $si->execute();
        }
        return [$sid,$total];
    }

    public function find(int $id): array {
        $h=$this->db->query("SELECT * FROM sales WHERE id=$id")->fetch_assoc() ?: [];
        $it=[]; $r=$this->db->query("SELECT * FROM sale_items WHERE sale_id=$id");
        while($x=$r->fetch_assoc()) $it[]=$x;
        return ['header'=>$h,'items'=>$it];
    }
}
