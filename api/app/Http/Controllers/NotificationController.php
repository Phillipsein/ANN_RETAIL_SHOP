<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Perms;
use App\Repositories\NotificationRepository;

class NotificationController {
    public static function mine(array $user): void { $r=new NotificationRepository(); Response::json($r->listForUser((int)$user['sub'])); }
    public static function read(array $user,array $params): void { $r=new NotificationRepository(); $ok=$r->markRead((int)$params['id']); Response::json(['ok'=>$ok]); }
}
