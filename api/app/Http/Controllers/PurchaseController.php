<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\PurchaseRepository; use App\Services\InventoryService;

class PurchaseController {
    public static function create(array $user): void {
        Perms::require($user,'PURCHASE_CREATE');
        $in = Request::jsonBody();
        $h = [
            'store_id'=>(int)($in['store_id'] ?? 1),
            'supplier_id'=>(int)$in['supplier_id'],
            'invoice_no'=>$in['invoice_no'] ?? null,
            'invoice_date'=>$in['invoice_date'],
            'transport_cost'=>(int)($in['transport_cost'] ?? 0),
            'other_cost'=>(int)($in['other_cost'] ?? 0),
            'total_cost'=>(int)($in['total_cost'] ?? 0),
            'created_by'=>(int)$user['sub']
        ];
        $items = $in['items'] ?? [];
        $repo = new PurchaseRepository();
        $pid = $repo->createPurchase($h,$items);
        if (!$pid) Response::json(['error'=>'create failed'],500);
        // Post to inventory
        (new InventoryService())->postPurchase($pid,$h['store_id'],$items);
        Response::json(['ok'=>true,'id'=>$pid],201);
    }

    public static function list(array $user): void {
        Perms::require($user,'PURCHASE_VIEW');
        $repo = new PurchaseRepository();
        $out = $repo->list(['from'=>$_GET['from'] ?? null, 'to'=>$_GET['to'] ?? null]);
        Response::json($out);
    }
    public static function getOne(array $user,array $params): void {
        Perms::require($user,'PURCHASE_VIEW');
        $repo = new PurchaseRepository(); Response::json($repo->find((int)$params['id']));
    }
}
