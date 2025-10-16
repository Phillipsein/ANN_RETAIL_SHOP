<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\RBACRepository;

class RBACController {
    public static function roles(array $user): void {
        Perms::require($user,'ROLE_VIEW');
        $r=new RBACRepository(); Response::json($r->listRoles());
    }
    public static function permissions(array $user): void {
        Perms::require($user,'ROLE_VIEW');
        $r=new RBACRepository(); Response::json($r->listPermissions());
    }
    public static function assignRole(array $user,array $params): void {
        Perms::require($user,'PERMISSION_ASSIGN');
        $in=Request::jsonBody(); $uid=(int)($in['user_id'] ?? 0); $rid=(int)($in['role_id'] ?? 0);
        $r=new RBACRepository(); $ok=$r->assignRole($uid,$rid); Response::json(['ok'=>$ok]);
    }
    public static function grantPerm(array $user,array $params): void {
        Perms::require($user,'PERMISSION_ASSIGN');
        $in=Request::jsonBody(); $rid=(int)($in['role_id'] ?? 0); $pid=(int)($in['permission_id'] ?? 0);
        $r=new RBACRepository(); $ok=$r->grantPermissionToRole($rid,$pid); Response::json(['ok'=>$ok]);
    }
}
