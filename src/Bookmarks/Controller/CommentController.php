<?php
/**
 * Created by PhpStorm.
 * User: qzich
 * Date: 9/11/16
 * Time: 9:03 PM
 */

namespace Bookmarks\Controller;


use MongoDB\Client;
use Symfony\Component\HttpFoundation\Request;
use MongoDB\BSON\ObjectID;

class CommentController
{
    /** @var \MongoDB\Collection */
    private $bookmarkCollection;

    /** @var \MongoDB\Collection */
    private $commentsCollection;

    public function __construct()
    {
        $client = new Client();
        $this->bookmarkCollection = $client->rest->bookmarks;
        $this->commentsCollection = $client->rest->comments;
    }

    /**
     * Add comment to bookmark by bookmark id
     *
     * @param $uid
     * @param Request $request
     * @return array
     */
    public function addComment($uid, Request $request)
    {
        $bookmark = $this->bookmarkCollection->findOne(
            ['_id' => new ObjectID($uid)]
        );

        if ($bookmark) {
            $document = [
                'bookmarkId' => $uid,
                'text' => $request->request->get("text"),
                'created_at' => (new \DateTime())->getTimestamp(),
                'ip' => $request->getClientIp(),
            ];

            $comment = $this->commentsCollection->insertOne($document);

            return ['uid' => (string)$comment->getInsertedId()];
        }
    }

    /**
     * Update bookmark by commentId
     *
     * @param $uid
     * @param Request $request
     */
    public function updateComment($uid, Request $request)
    {
        $comment = $this->commentsCollection->findOne(['_id' => new ObjectID($uid)]);

        $currentTimestamp = (new \DateTime())->getTimestamp();

        if ($comment && $request->getClientIp(
            ) == $comment['ip'] && $currentTimestamp - $comment['created_at'] < 3600
        ) {

            $this->commentsCollection->updateOne(
                ['_id' => new ObjectID($uid)],
                ['$set' => ['text' => $request->request->get("text")]]
            );
        }
    }

    /**
     * Remove comment by commentId
     *
     * @param $uid
     * @param Request $request
     */
    public function removeComment($uid, Request $request)
    {
        $comment = $this->commentsCollection->findOne(['_id' => new ObjectID($uid)]);

        $currentTimestamp = (new \DateTime())->getTimestamp();

        if ($comment && $request->getClientIp(
            ) == $comment['ip'] && $currentTimestamp - $comment['created_at'] < 3600
        ) {

            $this->commentsCollection->deleteOne(['_id' => new ObjectID($uid)]);
        }
    }

}