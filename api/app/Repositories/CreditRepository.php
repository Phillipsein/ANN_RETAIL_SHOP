<?php
namespace App\Repositories;
use App\Database\Connection; use mysqli;

class CreditRepository {
    private mysqli $db;
    public function __construct(){ $this->db = Connection::get(); }

    public function createCredit(int $customerId,int $saleId,int $amount, ?string $dueDate): int {
        $s=$this->db->prepare("INSERT INTO customer_credits (customer_id,sale_id,due_date,amount,balance) VALUES (?,?,?,?,?)");
        $s->bind_param('iisii',$customerId,$saleId,$dueDate,$amount,$amount);
        if(!$s->execute()) return 0; return $s->insert_id;
    }
    public function addPayment(int $customerId,int $creditId,int $amount,int $receivedBy,string $method,?string $ref): int {
        $s=$this->db->prepare("INSERT INTO customer_payments (customer_id,credit_id,paid_amount,method,reference,received_by) VALUES (?,?,?,?,?,?)");
        $s->bind_param('iiissi',$customerId,$creditId,$amount,$method,$ref,$receivedBy);
        if(!$s->execute()) return 0;
        $this->db->query("UPDATE customer_credits SET balance = GREATEST(0, balance-$amount) WHERE id=$creditId");
        return $s->insert_id;
    }
}
