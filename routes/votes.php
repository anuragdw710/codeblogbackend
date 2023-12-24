<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Add a vote for a comment
$app->post('/api/votes/add', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $commentId = $data['comment_id'];
    $userId = $data['user_id'];
    $voteType = $data['vote_type'];
    // Delete existing vote for the current user if any
    $deleteSql = "DELETE FROM votes WHERE comment_id = :commentId AND user_id = :userId";
    try {
        $db = new db();
        $pdo = $db->connect();
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->bindParam(':commentId', $commentId);
        $deleteStmt->bindParam(':userId', $userId);
        $deleteStmt->execute();
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => 'Error deleting existing vote', 'error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // Add new vote
    $addSql = "INSERT INTO votes (comment_id, user_id, vote_type) VALUES (:commentId, :userId, :voteType)";

    try {
        $addStmt = $pdo->prepare($addSql);
        $addStmt->bindParam(':commentId', $commentId);
        $addStmt->bindParam(':userId', $userId);
        $addStmt->bindParam(':voteType', $voteType);
        $addStmt->execute();

        $response->getBody()->write(json_encode(['msg' => 'Vote added successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => 'Error adding vote', 'error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Get vote_type for a specific user_id and comment_id
$app->get('/api/votes/{comment_id}/{user_id}', function (Request $request, Response $response, array $args) {
    $commentId = $args['comment_id'];
    $userId = $args['user_id'];

    $sql = "SELECT vote_type FROM votes WHERE comment_id = :commentId AND user_id = :userId LIMIT 1;";

    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':commentId', $commentId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // User_id with comment_id is present
            $voteType = $result['vote_type'];
        } else {
            // User_id with comment_id is not present
            $voteType = '0';
        }

        $response->getBody()->write(json_encode(['vote_type' => $voteType]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});
