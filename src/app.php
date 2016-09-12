<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Response;

$routes = new Routing\RouteCollection();

$routes->add(
    "bookmarks",
    new Routing\Route(
        '/api/bookmarks',
        [
            '_controller' => 'Bookmarks\\Controller\\BookmarkController::getBookmarks',
        ], [], [], '', [], "get"
    )
);

$routes->add(
    "bookmarks.get",
    new Routing\Route(
        '/api/bookmark/{uid}',
        [
            '_controller' => 'Bookmarks\\Controller\\BookmarkController::getBookmark',
        ], [], [], '', [], "get"
    )
);

$routes->add(
    "bookmarks.add",
    new Routing\Route(
        '/api/bookmark',
        [
            '_controller' => 'Bookmarks\\Controller\\BookmarkController::addBookmark',
        ], [], [], '', [], "post"
    )
);

$routes->add(
    "comments.add",
    new Routing\Route(
        '/api/comment/{uid}',
        [
            '_controller' => 'Bookmarks\\Controller\\CommentController::addComment',
        ], [], [], '', [], "post"
    )
);

$routes->add(
    "comments.update",
    new Routing\Route(
        '/api/comment/{uid}',
        [
            '_controller' => 'Bookmarks\\Controller\\CommentController::updateComment',
        ], [], [], '', [], "put"
    )
);

$routes->add(
    "comments.delete",
    new Routing\Route(
        '/api/comment/{uid}',
        [
            '_controller' => 'Bookmarks\\Controller\\CommentController::removeComment',
        ], [], [], '', [], "delete"
    )
);

return $routes;