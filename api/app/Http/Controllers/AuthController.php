<?php
namespace App\Http\Controllers;
use App\Support\Response; use App\Support\Request;
use App\Repositories\UserRepository; use App\Services\AuthService;

class AuthController {
    public static function registerFirst(): void {
        $users = new UserRepository();
        if ($users->countUsers() > 0) Response::json(['error'=>'Users already exist'], 400);
        $in = Request::jsonBody();
        $name=trim($in['name'] ?? ''); $email=trim($in['email'] ?? ''); $password=$in['password'] ?? '';
        if ($name===''||$email===''||$password==='') Response::json(['error'=>'name, email, password required'],422);
        $hash = password_hash($password, PASSWORD_BCRYPT);
        if(!$users->createOwner($name,$email,$hash)) Response::json(['error'=>'Failed to create user'],500);
        Response::json(['ok'=>true]);
    }
    public static function login(): void {
        $users = new UserRepository();
        $in = Request::jsonBody();
        $email=trim($in['email'] ?? ''); $password=$in['password'] ?? '';
        if ($email===''||$password==='') Response::json(['error'=>'email and password required'],422);
        $u=$users->findByEmail($email);
        if (!$u || !password_verify($password,$u['password_hash']) || (int)$u['status']!==1) Response::json(['error'=>'Invalid credentials'],401);
        $roles=$users->getRoles((int)$u['id']); $perms=$users->getPermissions((int)$u['id']);
        $token=AuthService::issueToken($u,$roles,$perms);
        Response::json(['token'=>$token,'user'=>['id'=>(int)$u['id'],'name'=>$u['name'],'email'=>$u['email']],'roles'=>$roles,'permissions'=>$perms]);
    }
}
