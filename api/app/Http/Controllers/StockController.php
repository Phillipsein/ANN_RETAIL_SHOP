<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\InventoryRepository; use App\Services\InventoryService;
use App\Database\Connection;

class StockController {
    public static function adjust(array $user): void {
        Perms::require($user,'INVENTORY_ADJUST');
        $in = Request::jsonBody();
        $db = Connection::get();
        $s=$db->prepare("INSERT INTO stock_adjustments (store_id,product_id,qty_diff,reason,note,created_by) VALUES (?,?,?,?,?,?)");
        $store=(int)($in['store_id'] ?? 1); $pid=(int)$in['product_id']; $qty=(int)$in['qty_diff']; $reason=$in['reason'] ?? 'COUNT'; $note=$in['note'] ?? null; $uid=(int)$user['sub'];
        $s->bind_param('iiissi',$store,$pid,$qty,$reason,$note,$uid);
        if(!$s->execute()) Response::json(['error'=>'adjust failed'],500);
        $id=$s->insert_id;
        (new InventoryService())->postAdjustment($id,$store,$pid,$qty, (int)($in['unit_cost'] ?? 0));
        Response::json(['ok'=>true,'id'=>$id],201);
    }
}
