<?php
namespace App\Services;
use App\Repositories\NotificationRepository;

class NotificationService {
    private NotificationRepository $repo;
    public function __construct(){ $this->repo = new NotificationRepository(); }

    public function salePosted(int $ownerUserId, array $payload): void {
        $this->repo->create($ownerUserId, 'SALE_POSTED', $payload);
        $this->pushFCM($ownerUserId, 'New Sale', 'A sale was posted', $payload);
    }

    private function pushFCM(int $userId, string $title, string $body, array $data): void {
        $key = getenv('FIREBASE_SERVER_KEY'); if (!$key) return;
        // Minimal example: would normally look up user device tokens; kept simple for now.
        // Implement token querying and send via FCM HTTP v1 or legacy endpoint.
    }
}
