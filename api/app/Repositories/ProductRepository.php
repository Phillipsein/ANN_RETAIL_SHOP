<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class ProductRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function list(string $q=''): array {
        if ($q!==''){
            $like='%'.$this->db->real_escape_string($q).'%';
            $s=$this->db->prepare("SELECT id,name,sku,short_code,barcode,unit,price,reorder_level FROM products WHERE is_active=1 AND (name LIKE ? OR sku LIKE ? OR short_code LIKE ?) ORDER BY name LIMIT 200");
            $s->bind_param('sss',$like,$like,$like); $s->execute(); $res=$s->get_result();
        } else {
            $res=$this->db->query("SELECT id,name,sku,short_code,barcode,unit,price,reorder_level FROM products WHERE is_active=1 ORDER BY name LIMIT 200");
        }
        $rows=[]; while($row=$res->fetch_assoc()) $rows[]=$row; return $rows;
    }

    public function create(array $d): int {
        $s=$this->db->prepare("INSERT INTO products (name, sku, short_code, barcode, unit, price, reorder_level) VALUES (?,?,?,?,?,?,?)");
        $name=trim($d['name'] ?? ''); $sku=$d['sku'] ?? null; $short=$d['short_code'] ?? null; $barcode=$d['barcode'] ?? null;
        $unit=$d['unit'] ?? 'pcs'; $price=(int)($d['price'] ?? 0); $reorder=(int)($d['reorder_level'] ?? 0);
        if ($sku==='') $sku=null; if ($short==='') $short=null; if ($barcode==='') $barcode=null;
        $s->bind_param('sssssis',$name,$sku,$short,$barcode,$unit,$price,$reorder);
        if(!$s->execute()) return 0; return $s->insert_id;
    }

    public function find(int $id): ?array {
        $s=$this->db->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
        $s->bind_param('i',$id); $s->execute(); $r=$s->get_result(); $x=$r->fetch_assoc(); return $x ?: null;
    }

    public function update(int $id, array $d): bool {
        $s=$this->db->prepare("UPDATE products SET name=?, sku=?, short_code=?, barcode=?, unit=?, price=?, reorder_level=? WHERE id=?");
        $name=trim($d['name'] ?? ''); $sku=$d['sku'] ?? null; $short=$d['short_code'] ?? null; $barcode=$d['barcode'] ?? null;
        $unit=$d['unit'] ?? 'pcs'; $price=(int)($d['price'] ?? 0); $reorder=(int)($d['reorder_level'] ?? 0);
        if ($sku==='') $sku=null; if ($short==='') $short=null; if ($barcode==='') $barcode=null;
        $s->bind_param('ssssssii',$name,$sku,$short,$barcode,$unit,$price,$reorder,$id);
        return $s->execute();
    }
}
