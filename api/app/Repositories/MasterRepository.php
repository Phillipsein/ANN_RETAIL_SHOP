<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class MasterRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    // Categories
    public function categories(): array {
        $r=$this->db->query("SELECT id,name,parent_id FROM categories ORDER BY name"); $o=[]; while($x=$r->fetch_assoc()) $o[]=$x; return $o;
    }
    public function addCategory(string $name, ?int $parentId): int {
        $s=$this->db->prepare("INSERT INTO categories (name,parent_id) VALUES (?,?)");
        $s->bind_param('si',$name,$parentId); if(!$s->execute()) return 0; return $s->insert_id;
    }
    public function updateCategory(int $id,string $name, ?int $parentId): bool {
        $s=$this->db->prepare("UPDATE categories SET name=?, parent_id=? WHERE id=?");
        $s->bind_param('sii',$name,$parentId,$id); return $s->execute();
    }
    public function deleteCategory(int $id): bool {
        $s=$this->db->prepare("DELETE FROM categories WHERE id=?"); $s->bind_param('i',$id); return $s->execute();
    }

    // Units (suggestions only)
    public function units(): array {
        $r=$this->db->query("SELECT id,name,is_system FROM units ORDER BY name"); $o=[]; while($x=$r->fetch_assoc()) $o[]=$x; return $o;
    }
    public function addUnit(string $name, int $isSystem=0): int {
        $s=$this->db->prepare("INSERT IGNORE INTO units (name,is_system) VALUES (?,?)");
        $s->bind_param('si',$name,$isSystem); $s->execute(); return $s->insert_id ?: 0;
    }

    // Suppliers
    public function suppliers(): array {
        $r=$this->db->query("SELECT id,name,phone,contact_name FROM suppliers ORDER BY name"); $o=[]; while($x=$r->fetch_assoc()) $o[]=$x; return $o;
    }
    public function addSupplier(string $name, ?string $phone, ?string $contact): int {
        $s=$this->db->prepare("INSERT INTO suppliers (name,phone,contact_name) VALUES (?,?,?)");
        $s->bind_param('sss',$name,$phone,$contact); if(!$s->execute()) return 0; return $s->insert_id;
    }

    // Customers
    public function customers(): array {
        $r=$this->db->query("SELECT id,name,phone FROM customers ORDER BY name"); $o=[]; while($x=$r->fetch_assoc()) $o[]=$x; return $o;
    }
    public function addCustomer(string $name, ?string $phone): int {
        $s=$this->db->prepare("INSERT INTO customers (name,phone) VALUES (?,?)");
        $s->bind_param('ss',$name,$phone); if(!$s->execute()) return 0; return $s->insert_id;
    }

    // Stores
    public function stores(): array {
        $r=$this->db->query("SELECT id,name,location,is_active FROM stores ORDER BY id"); $o=[]; while($x=$r->fetch_assoc()) $o[]=$x; return $o;
    }
}
