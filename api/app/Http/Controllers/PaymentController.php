<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\PaymentRepository;

class PaymentController {
    public static function methods(array $user): void { Perms::require($user,'PAYMENT_METHOD_MANAGE'); $r=new PaymentRepository(); Response::json($r->methods()); }
    public static function log(array $user): void { Perms::require($user,'PAYMENT_TX_VIEW'); $in=Request::jsonBody(); $r=new PaymentRepository(); $id=$r->logTxn($in); Response::json(['ok'=>$id>0,'id'=>$id], $id?201:500); }
    public static function webhook($params): void {
        $provider = $params['provider'] ?? 'UNKNOWN'; $type = $_GET['type'] ?? 'event';
        $payload = file_get_contents('php://input') ?: '{}';
        $r=new PaymentRepository(); $id=$r->webhook($provider,$type,$payload); Response::json(['ok'=>$id>0]);
    }
}
