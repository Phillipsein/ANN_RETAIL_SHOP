<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class PaymentRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function methods(): array {
        $r=$this->db->query("SELECT id,code,display_name FROM payment_methods ORDER BY id");
        $o=[]; while($x=$r->fetch_assoc()) $o[]=$x; return $o;
    }
    public function logTxn(array $d): int {
        $s=$this->db->prepare("INSERT INTO payment_transactions (sale_id,method_id,provider,provider_ref,payer_phone,amount,status,raw_payload) VALUES (?,?,?,?,?,?,?,?)");
        $rp = json_encode($d['raw_payload'] ?? null);
        $s->bind_param('iisssiss',$d['sale_id'],$d['method_id'],$d['provider'],$d['provider_ref'],$d['payer_phone'],$d['amount'],$d['status'],$rp);
        if(!$s->execute()) return 0; return $s->insert_id;
    }
    public function webhook(string $provider,string $type,string $payload): int {
        $s=$this->db->prepare("INSERT INTO webhook_events (provider,event_type,payload_json) VALUES (?,?,?)");
        $s->bind_param('sss',$provider,$type,$payload); if(!$s->execute()) return 0; return $s->insert_id;
    }
}
