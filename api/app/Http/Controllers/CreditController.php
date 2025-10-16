<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\CreditRepository;

class CreditController {
    public static function pay(array $user): void {
        Perms::require($user,'CREDIT_SALE_CREATE'); // and payment rights
        $in=Request::jsonBody();
        $repo=new CreditRepository();
        $id=$repo->addPayment((int)$in['customer_id'], (int)$in['credit_id'], (int)$in['amount'], (int)$user['sub'], $in['method'] ?? 'CASH', $in['reference'] ?? null);
        if(!$id) Response::json(['error'=>'payment failed'],500); Response::json(['ok'=>true,'id'=>$id],201);
    }
}
