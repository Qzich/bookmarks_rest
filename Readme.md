API routing documentation

Bookmarks:<br>
    get all bookmarks: GET /api/bookmarks <br>
    get specific bookmark: GET /api/bookmark/{uid} where {uid} is bookmark id <br>
    add bookmark: POST /api/bookmark where {url} is bookmark url  <br>

Comments:<br>
    add comment to bookmark: POST /api/comment/{uid} where {uid} is bookmark id<br>
    update comment: PUT /api/comment/{uid} where {uid} is comment id<br>
    delete comment: DELETE /api/comment/{uid} where {uid} is comment id<br>
    