<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class SettingsRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }
    public function getAll(): array {
        $r=$this->db->query("SELECT `key`,`value`,updated_at FROM system_settings");
        $o=[]; while($x=$r->fetch_assoc()) $o[$x['key']]=$x['value']; return $o;
    }
    public function set(string $key,string $value): bool {
        $s=$this->db->prepare("INSERT INTO system_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
        $s->bind_param('ss',$key,$value); return $s->execute();
    }
}
