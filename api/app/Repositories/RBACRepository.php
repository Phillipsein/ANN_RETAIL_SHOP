<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class RBACRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function listRoles(): array {
        $r=$this->db->query("SELECT id,name,description FROM roles ORDER BY id"); $out=[]; while($x=$r->fetch_assoc()) $out[]=$x; return $out;
    }
    public function listPermissions(): array {
        $r=$this->db->query("SELECT id,code,description FROM permissions ORDER BY code"); $out=[]; while($x=$r->fetch_assoc()) $out[]=$x; return $out;
    }
    public function assignRole(int $userId,int $roleId): bool {
        $s=$this->db->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?,?)");
        $s->bind_param('ii',$userId,$roleId); return $s->execute();
    }
    public function grantPermissionToRole(int $roleId,int $permId): bool {
        $s=$this->db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?,?)");
        $s->bind_param('ii',$roleId,$permId); return $s->execute();
    }
}
