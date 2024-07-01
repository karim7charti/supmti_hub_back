<?php


namespace App\Service\RealtimeServices;


use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class RealtimeCommentsService
{
    private static $base_topic='comments/';
    public function __construct(private HubInterface $hub)
    {
    }

    public function push_activity_comment_count(int $activity_id):void{
        $update = new Update(
            $this::$base_topic.'count/'.$activity_id,
            json_encode(['count' => 1])
        );
        $this->hub->publish($update);
    }
    public function push_comment(array $comment,int $activity_id):void{
        $update = new Update(
            $this::$base_topic.'comment/'.$activity_id,
            json_encode(['comment' => $comment])

        );
        $this->hub->publish($update);
    }
}