<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\SalesRepository; use App\Repositories\CreditRepository;
use App\Services\InventoryService; use App\Database\Connection;

class SalesController {
    public static function create(array $user): void {
        Perms::require($user,'SALES_CREATE');
        $in = Request::jsonBody();
        $store=(int)$in['store_id']; $pay=$in['pay_method'];
        $items = $in['items'] ?? [];
        $db = Connection::get();
        // Fetch moving_avg_cost for each product to stamp cost_at_sale
        foreach ($items as &$it) {
            $pid=(int)$it['product_id'];
            $r=$db->query("SELECT moving_avg_cost FROM products WHERE id=$pid");
            $row=$r->fetch_assoc(); $it['cost_at_sale']=(int)($row['moving_avg_cost'] ?? 0);
            if (!isset($it['discount'])) $it['discount']=0;
        }
        unset($it);
        $h=[
            'store_id'=>$store,
            'cashier_id'=>(int)$user['sub'],
            'sale_no'=>'S-'.date('Ymd-His'),
            'sale_time'=>date('Y-m-d H:i:s'),
            'pay_method'=>$pay,
            'customer_id'=> $pay==='CREDIT' ? (int)($in['customer_id'] ?? 0) : null,
            'paid_amount'=>(int)($in['paid_amount'] ?? 0),
            'discount_total'=>(int)($in['discount_total'] ?? 0),
            'notes'=>$in['notes'] ?? null
        ];
        $repo = new SalesRepository();
        [$saleId,$total] = $repo->createSale($h,$items);
        if(!$saleId) Response::json(['error'=>'create failed'],500);
        // Post inventory
        $cogs = (new InventoryService())->postSale($saleId,$store,$items);
        // Handle credit account if needed
        if ($pay==='CREDIT') {
            $creditRepo = new CreditRepository();
            $creditRepo->createCredit((int)$h['customer_id'],$saleId,$total, $in['due_date'] ?? null);
        }
        Response::json(['ok'=>true,'id'=>$saleId,'sale_no'=>$h['sale_no'],'total'=>$total,'cogs'=>$cogs],201);
    }

    public static function getOne(array $user,array $params): void {
        Perms::require($user,'SALES_VIEW'); $repo=new SalesRepository(); Response::json($repo->find((int)$params['id']));
    }
}
