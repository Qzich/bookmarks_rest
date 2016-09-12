<?php
/**
 * Created by PhpStorm.
 * User: qzich
 * Date: 9/11/16
 * Time: 7:24 PM
 */

namespace Bookmarks\Controller;


use MongoDB\BSON\ObjectID;
use MongoDB\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookmarkController
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
     * Gets last ten bookmarks
     *
     * @return JsonResponse
     */
    public function getBookmarks()
    {
        $result = [];
        $i = 0;
        foreach ($this->bookmarkCollection->find() as $bookmark) {
            if ($i >= 10) {
                break;
            }

            $result[] = [
                'uid' => (string)$bookmark['_id'],
                'url' => $bookmark['url'],
                'created_at' => $bookmark['created_at'],
            ];

            $i++;
        }

        return $result;
    }

    /**
     * Gets bookmark with comments
     *
     * @param $uid Bookmark id
     * @return array
     */
    public function getBookmark($uid)
    {
        $result = $this->bookmarkCollection->findOne(
            ['_id' => new ObjectID($uid)]
        );

        if ($result) {
            $bookmark = [
                'uid' => (string)$result['_id'],
                'url' => $result['url'],
                'created_at' => $result['created_at'],
                'comments' => [],
            ];

            $comments = $this->commentsCollection->find(['bookmarkId' => $uid]);

            foreach ($comments as $comment) {
                $bookmark['comments'][] = [
                    'uid' => (string)$result['_id'],
                    'text' => $comment["text"],
                    'created_at' => $comment["created_at"],
                    'ip' => $comment["ip"],
                ];
            }

            return $bookmark;
        }
    }


    /**
     * Add new bookmark
     *
     * @param Request $request
     * @return mixed
     */
    public function addBookmark(Request $request)
    {
        if ($this->bookmarkCollection->count(['url' => $request->request->get("url")])) {
            return [
                'uid' => (string)$this->bookmarkCollection->findOne(
                    ['url' => $request->request->get("url")]
                )['_id'],
            ];
        }
        $document = [
            'url' => $request->request->get("url"),
            'created_at' => (new \DateTime())->getTimestamp(),
            'comments' => [],
        ];
        $result = $this->bookmarkCollection->insertOne($document);

        return ['uid' => (string)$result->getInsertedId()];
    }

}