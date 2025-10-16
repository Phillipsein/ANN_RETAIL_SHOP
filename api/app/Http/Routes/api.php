<?php
/** @var \App\Support\Router $router */
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RBACController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;

// Auth
$router->add('POST','/auth/register-first',[AuthController::class,'registerFirst']);
$router->add('POST','/auth/login',[AuthController::class,'login']);

// RBAC
$router->add('GET','/rbac/roles',[RBACController::class,'roles'], auth:true);
$router->add('GET','/rbac/permissions',[RBACController::class,'permissions'], auth:true);
$router->add('POST','/rbac/assign-role',[RBACController::class,'assignRole'], auth:true);
$router->add('POST','/rbac/grant-permission',[RBACController::class,'grantPerm'], auth:true);

// Master data
$router->add('GET','/categories',[MasterController::class,'categories'], auth:true);
$router->add('POST','/categories',[MasterController::class,'addCategory'], auth:true);
$router->add('PUT','/categories/:id',[MasterController::class,'updateCategory'], auth:true);
$router->add('DELETE','/categories/:id',[MasterController::class,'deleteCategory'], auth:true);

$router->add('GET','/units',[MasterController::class,'units'], auth:true);
$router->add('POST','/units',[MasterController::class,'addUnit'], auth:true);

$router->add('GET','/suppliers',[MasterController::class,'suppliers'], auth:true);
$router->add('POST','/suppliers',[MasterController::class,'addSupplier'], auth:true);

$router->add('GET','/customers',[MasterController::class,'customers'], auth:true);
$router->add('POST','/customers',[MasterController::class,'addCustomer'], auth:true);

$router->add('GET','/stores',[MasterController::class,'stores'], auth:true);

$router->add('GET','/products',[MasterController::class,'productsList'], auth:true);
$router->add('POST','/products',[MasterController::class,'productCreate'], auth:true);
$router->add('PUT','/products/:id',[MasterController::class,'productUpdate'], auth:true);

// Inventory / Purchasing
$router->add('POST','/purchases',[PurchaseController::class,'create'], auth:true);
$router->add('GET','/purchases',[PurchaseController::class,'list'], auth:true);
$router->add('GET','/purchases/:id',[PurchaseController::class,'getOne'], auth:true);
$router->add('POST','/stock-adjustments',[StockController::class,'adjust'], auth:true);

// Sales
$router->add('POST','/sales',[SalesController::class,'create'], auth:true);
$router->add('GET','/sales/:id',[SalesController::class,'getOne'], auth:true);

// Cash & Credit
$router->add('POST','/cash-sessions/open',[CashController::class,'open'], auth:true);
$router->add('POST','/cash-sessions/:id/close',[CashController::class,'close'], auth:true);
$router->add('POST','/cash-transactions',[CashController::class,'addTx'], auth:true);
$router->add('POST','/credit/pay',[CreditController::class,'pay'], auth:true);

// Payments & Webhooks
$router->add('GET','/payment-methods',[PaymentController::class,'methods'], auth:true);
$router->add('POST','/payment-transactions',[PaymentController::class,'log'], auth:true);
$router->add('POST','/webhooks/:provider',[PaymentController::class,'webhook']); // public endpoint

// Settings
$router->add('GET','/settings',[SettingsController::class,'all'], auth:true);
$router->add('POST','/settings',[SettingsController::class,'set'], auth:true);

// Notifications
$router->add('GET','/notifications',[NotificationController::class,'mine'], auth:true);
$router->add('POST','/notifications/:id/read',[NotificationController::class,'read'], auth:true);

// Reports
$router->add('GET','/reports/daily',[ReportController::class,'daily'], auth:true);
$router->add('GET','/reports/monthly',[ReportController::class,'monthly'], auth:true);
