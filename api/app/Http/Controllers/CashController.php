<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\CashRepository;

class CashController {
    public static function open(array $user): void {
        Perms::require($user,'CASH_SESSION_OPEN');
        $in=Request::jsonBody(); $repo=new CashRepository();
        $id=$repo->openSession((int)($in['store_id'] ?? 1),(int)$user['sub'], (int)($in['opening_float'] ?? 0));
        if(!$id) Response::json(['error'=>'open failed'],500); Response::json(['ok'=>true,'id'=>$id],201);
    }
    public static function close(array $user,array $params): void {
        Perms::require($user,'CASH_SESSION_CLOSE');
        $in=Request::jsonBody(); $repo=new CashRepository();
        $ok=$repo->closeSession((int)$params['id'], (int)($in['counted_cash'] ?? 0), (int)($in['counted_momo'] ?? 0));
        Response::json(['ok'=>$ok]);
    }
    public static function addTx(array $user): void {
        Perms::require($user,'CASH_TX_CREATE');
        $in=Request::jsonBody(); $repo=new CashRepository();
        $d=[
            'store_id'=>(int)($in['store_id'] ?? 1),
            'cashier_id'=>(int)$user['sub'],
            'session_id'=>(int)($in['session_id'] ?? 0),
            'tx_type'=>$in['tx_type'] ?? 'EXPENSE',
            'method'=>$in['method'] ?? 'CASH',
            'amount'=>(int)$in['amount'],
            'reason'=>$in['reason'] ?? null,
            'reference'=>$in['reference'] ?? null
        ];
        $id=$repo->addTx($d); if(!$id) Response::json(['error'=>'tx failed'],500); Response::json(['ok'=>true,'id'=>$id],201);
    }
}
