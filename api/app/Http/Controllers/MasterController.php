<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\MasterRepository; use App\Repositories\ProductRepository;

class MasterController {
    public static function categories(array $user): void { Perms::require($user,'CATEGORY_VIEW'); $m=new MasterRepository(); Response::json($m->categories()); }
    public static function addCategory(array $user): void { Perms::require($user,'CATEGORY_CREATE'); $in=Request::jsonBody(); $m=new MasterRepository(); $id=$m->addCategory(trim($in['name']), $in['parent_id'] ?? null); Response::json(['ok'=>$id>0,'id'=>$id],$id?201:500); }
    public static function updateCategory(array $user,array $params): void { Perms::require($user,'CATEGORY_EDIT'); $in=Request::jsonBody(); $m=new MasterRepository(); $ok=$m->updateCategory((int)$params['id'], trim($in['name']), $in['parent_id'] ?? null); Response::json(['ok'=>$ok]); }
    public static function deleteCategory(array $user,array $params): void { Perms::require($user,'CATEGORY_DELETE'); $m=new MasterRepository(); $ok=$m->deleteCategory((int)$params['id']); Response::json(['ok'=>$ok]); }

    public static function units(array $user): void { Perms::require($user,'UNIT_MANAGE'); $m=new MasterRepository(); Response::json($m->units()); }
    public static function addUnit(array $user): void { Perms::require($user,'UNIT_MANAGE'); $in=Request::jsonBody(); $m=new MasterRepository(); $id=$m->addUnit(trim($in['name']), (int)($in['is_system'] ?? 0)); Response::json(['ok'=>$id>0,'id'=>$id],$id?201:500); }

    public static function suppliers(array $user): void { Perms::require($user,'SUPPLIER_VIEW'); $m=new MasterRepository(); Response::json($m->suppliers()); }
    public static function addSupplier(array $user): void { Perms::require($user,'SUPPLIER_CREATE'); $in=Request::jsonBody(); $m=new MasterRepository(); $id=$m->addSupplier(trim($in['name']), $in['phone'] ?? null, $in['contact_name'] ?? null); Response::json(['ok'=>$id>0,'id'=>$id],$id?201:500); }

    public static function customers(array $user): void { Perms::require($user,'CUSTOMER_VIEW'); $m=new MasterRepository(); Response::json($m->customers()); }
    public static function addCustomer(array $user): void { Perms::require($user,'CUSTOMER_CREATE'); $in=Request::jsonBody(); $m=new MasterRepository(); $id=$m->addCustomer(trim($in['name']), $in['phone'] ?? null); Response::json(['ok'=>$id>0,'id'=>$id],$id?201:500); }

    public static function stores(array $user): void { Perms::require($user,'STORE_VIEW'); $m=new MasterRepository(); Response::json($m->stores()); }

    public static function productsList(array $user): void { Perms::require($user,'PRODUCT_VIEW'); $repo=new ProductRepository(); $q=$_GET['q'] ?? ''; Response::json($repo->list($q)); }
    public static function productCreate(array $user): void { Perms::require($user,'PRODUCT_CREATE'); $in=Request::jsonBody(); $repo=new ProductRepository(); $id=$repo->create($in); if(!$id) Response::json(['error'=>'create failed'],500); Response::json(['ok'=>true,'id'=>$id],201); }
    public static function productUpdate(array $user,array $params): void { Perms::require($user,'PRODUCT_EDIT'); $in=Request::jsonBody(); $repo=new ProductRepository(); $ok=$repo->update((int)$params['id'],$in); Response::json(['ok'=>$ok]); }
}
