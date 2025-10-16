<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request; use App\Support\Perms;
use App\Repositories\SettingsRepository;

class SettingsController {
    public static function all(array $user): void { Perms::require($user,'SETTINGS_MANAGE'); $r=new SettingsRepository(); Response::json($r->getAll()); }
    public static function set(array $user): void { Perms::require($user,'SETTINGS_MANAGE'); $in=Request::jsonBody(); $r=new SettingsRepository(); $ok=$r->set($in['key'],$in['value']); Response::json(['ok'=>$ok]); }
}
