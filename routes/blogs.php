<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


// Get all blogs
$app->get('/api/blogs', function (Request $request, Response $response, array $args) {
    $sql = "SELECT page_id, title, content FROM pages";

    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->query($sql);
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($blogs));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});


// Get a specific blog post by ID
$app->get('/api/blogs/{blogPostId}', function (Request $request, Response $response, array $args) {
    $blogPostId = $args['blogPostId'];

    // Ensure that $blogPostId is a valid integer (you can add more validation if needed)
    if (!ctype_digit($blogPostId)) {
        $response->getBody()->write(json_encode(['msg' => 'Invalid blog post ID']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $sql = "SELECT page_id, title, content FROM pages WHERE page_id = :blogPostId";

    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':blogPostId', $blogPostId, PDO::PARAM_INT);
        $stmt->execute();
        $blogPost = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$blogPost) {
            $response->getBody()->write(json_encode(['msg' => 'Blog post not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Return the blog post data as JSON
        $responseData = [
            'blogPostId' => $blogPost['page_id'],
            'title' => $blogPost['title'],
            'content' => $blogPost['content'],
        ];

        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});
