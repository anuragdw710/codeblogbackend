<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


// Helper function to fetch a comment by ID
function fetchCommentById($commentId) {
    $sql = "SELECT
    c.comment_id,
    c.parent_id,
    c.user_id,
    u.user_name,
    c.content,
    c.timestamp,
    c.is_delete,
    c.depth_reply,
    SUM(CASE WHEN v.vote_type = '1' THEN 1 WHEN v.vote_type = '-1' THEN -1 ELSE 0 END) AS total_votes
    FROM
        comments c
    JOIN
        users u ON c.user_id = u.user_id
    LEFT JOIN
        votes v ON c.comment_id = v.comment_id
    WHERE 
        c.comment_id = ?
    GROUP BY
        c.comment_id
    ORDER BY
        c.comment_id ASC;
";
    
    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$commentId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    } catch (\PDOException $e) {
        // Handle error as needed
        return ['msg' => ['resp' => $e->getMessage()]];
    }
}

// Add a new comment ['user_id,'content','parent_id','depth_reply']   
$app->post('/api/comments/add/{blog_post_id}', function (Request $request, Response $response, array $args) {
    $blogPostId = $args['blog_post_id'];
    $parsedBody = $request->getParsedBody();
    $userId = $parsedBody['user_id']; 
    $content = $parsedBody['content'];
    $parentId=$parsedBody['parent_id'];
    $depthReply=$parsedBody['depth_reply'];
    $timestamp = date('Y-m-d H:i:s');
    $sql = "INSERT INTO comments (parent_id,page_id, user_id, content, timestamp,depth_reply) VALUES (?, ?, ?, ?,?,?)";

    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$parentId,$blogPostId, $userId, $content, $timestamp,$depthReply]);
        // Fetch the inserted comment for response
        $commentId = $pdo->lastInsertId();
        $comment = fetchCommentById($commentId);
        $response->getBody()->write(json_encode($comment));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});



// Get comments for a blog post
$app->get('/api/comments/{blog_post_id}', function (Request $request, Response $response, array $args) {
    $blogPostId = $args['blog_post_id'];
    $sql = "SELECT
    c.comment_id,
    c.parent_id,
    c.user_id,
    u.user_name,
    c.content,
    c.timestamp,
    c.is_delete,
    c.depth_reply,
    SUM(CASE WHEN v.vote_type = '1' THEN 1 WHEN v.vote_type = '-1' THEN -1 ELSE 0 END) AS total_votes
    FROM
        comments c
    JOIN
        users u ON c.user_id = u.user_id
    LEFT JOIN
        votes v ON c.comment_id = v.comment_id
    WHERE
        c.page_id = :blogPostId
    GROUP BY
        c.comment_id
    ORDER BY
        c.comment_id ASC;
    ";

    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':blogPostId', $blogPostId);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_OBJ);

        $response->getBody()->write(json_encode($comments));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});





// Edit a comment
$app->put('/api/comments/edit/{comment_id}', function (Request $request, Response $response, array $args) {
    $commentId = $args['comment_id'];
    $parsedBody = $request->getParsedBody();
    $newContent = $parsedBody['content'];

    // Assuming you have a timestamp column in your table
    $editableTimestamp = date('Y-m-d H:i:s', strtotime('-5 minutes'));

    $sql = "UPDATE comments SET content = ? WHERE comment_id = ? AND timestamp >= ?";
    
    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$newContent, $commentId, $editableTimestamp]);

        $response->getBody()->write(json_encode(['msg' => 'Comment updated successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});


//delete a comment 
$app->delete('/api/comments/delete/{comment_id}', function (Request $request, Response $response, array $args) {
    $commentId = $args['comment_id'];
    $sql = "UPDATE comments SET content = ? WHERE comment_id = ?";
    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["Message Deleted",$commentId]);
        $response->getBody()->write(json_encode(['msg' => 'Comment deleted successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});
