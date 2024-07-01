<?php


namespace App\Service\RealtimeServices;


use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class RealtimeLikeService
{
    private static $base_topic='https://example.com/activiies/likes/';
    public function __construct(private HubInterface $hub)
    {
    }
    public function push(int $activityId,int $count)
    {
        $update = new Update(
            $this::$base_topic.$activityId,
            json_encode(['count' => $count])
        );
        $this->hub->publish($update);
    }
}