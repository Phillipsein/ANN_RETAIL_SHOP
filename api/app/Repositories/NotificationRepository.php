<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class NotificationRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function create(int $userId,string $type,array $payload): int {
        $s=$this->db->prepare("INSERT INTO notifications (user_id,type,payload_json) VALUES (?,?,?)");
        $p = json_encode($payload);
        $s->bind_param('iss',$userId,$type,$p);
        if(!$s->execute()) return 0; return $s->insert_id;
    }
    public function listForUser(int $userId): array {
        $r=$this->db->query("SELECT id,type,payload_json,read_at,created_at FROM notifications WHERE user_id=$userId ORDER BY id DESC LIMIT 200");
        $o=[]; while($x=$r->fetch_assoc()) { $x['payload_json']=json_decode($x['payload_json'], true); $o[]=$x; } return $o;
    }
    public function markRead(int $id): bool {
        return $this->db->query("UPDATE notifications SET read_at=NOW() WHERE id=$id");
    }
}
