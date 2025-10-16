<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class UserRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function countUsers(): int {
        $res = $this->db->query("SELECT COUNT(*) c FROM users");
        $row = $res->fetch_assoc(); return (int)$row['c'];
    }
    public function createOwner(string $name, string $email, string $hash): bool {
        $s=$this->db->prepare("INSERT INTO users (name,email,password_hash,status) VALUES (?,?,?,1)");
        $s->bind_param('sss',$name,$email,$hash);
        if(!$s->execute()) return false; $uid=$s->insert_id;
        $s2=$this->db->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, 1)");
        $s2->bind_param('i',$uid); $s2->execute(); return true;
    }
    public function findByEmail(string $email): ?array {
        $s=$this->db->prepare("SELECT id,name,email,password_hash,status FROM users WHERE email=? LIMIT 1");
        $s->bind_param('s',$email); $s->execute();
        $r=$s->get_result(); $u=$r->fetch_assoc(); return $u ?: null;
    }
    public function getRoles(int $uid): array {
        $s=$this->db->prepare("SELECT r.name FROM roles r JOIN user_roles ur ON ur.role_id=r.id WHERE ur.user_id=?");
        $s->bind_param('i',$uid); $s->execute(); $r=$s->get_result();
        $out=[]; while($row=$r->fetch_assoc()) $out[]=$row['name']; return $out;
    }
    public function getPermissions(int $uid): array {
        $sql="SELECT DISTINCT p.code FROM permissions p JOIN role_permissions rp ON rp.permission_id=p.id JOIN user_roles ur ON ur.role_id=rp.role_id WHERE ur.user_id=?";
        $s=$this->db->prepare($sql); $s->bind_param('i',$uid); $s->execute(); $r=$s->get_result();
        $out=[]; while($row=$r->fetch_assoc()) $out[]=$row['code']; return $out;
    }
}
