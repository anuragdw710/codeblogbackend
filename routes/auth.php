<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/api/check-login', function (Request $request, Response $response) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // Check if the user is logged in
    $authorizationHeader = $request->getHeaderLine('Authorization');

    // Extract the token from the Authorization header
    $token = '';
    if (preg_match('/Bearer\s+(.*)$/i', $authorizationHeader, $matches)) {
        $token = $matches[1];
    }
    error_log(print_r($authorizationHeader, true));
    error_log(print_r($_SESSION, true));
    if (isset($_SESSION[$token])) {
        $userData = $_SESSION[$token];
        $userId = $userData['user_id'];
        $userName=$userData['user_name'];
        $responseData = [
            'success' => true,
            'user' => $userName,
            'userid' => $userId,
        ];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    } else {
        $responseData = [
            'success' => false,
        ];
      $response->getBody()->write(json_encode($responseData));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Get All Users Info for Admin only[]
$app->get('/api/users', function (Request $request, Response $response, array $args) {
    $sql = " SELECT * FROM users";

    try{
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        // echo json_encode($users);
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }catch(\PDOException $e){
        echo '{"msg" : {"resp": '.$e->getMessage().'}}';  
    }

});



// Add user for register user ['user_name, 'email', 'password]
$app->post('/api/users/add', function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();
    $user_name = $parsedBody['user_name'];
    $email = $parsedBody['email'];
    $password = $parsedBody['password'];

    $sql = "INSERT INTO users (user_name, email, password) VALUES (?, ?, ?)";

    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_name, $email, $password]);
        $response->getBody()->write(json_encode(['msg' => 'Success']));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// User login ['emial','password']
$app->post('/api/users/login', function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();
    $email = $parsedBody['email'];
    $password = $parsedBody['password'];
    $sql = "SELECT * FROM users WHERE email = ?";
    
    try {
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        if ($user && $password === $user->password) {
            $token = bin2hex(random_bytes(32));
             $_SESSION[$token] = [
                'user_id' => $user->user_id,
                'user_name' => $user->user_name,
            ];
            error_log(print_r($_SESSION, true));
            $response->getBody()->write(
                json_encode(['msg' => 'Login successful',
                                    'user_name'=>$user->user_name,
                                    'user_id'=>$user->user_id,
                                    'email'=>$user->email,
                                    'password'=>$user->password,
                                    'token' => $token]));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['msg' => 'Invalid email or password']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['msg' => ['resp' => $e->getMessage()]]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Logout
$app->get('/api/users/logout', function (Request $request, Response $response, array $args) {
    try {
        session_destroy();
        $response->getBody()->write(json_encode(['msg' => 'Logout successful']));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    } catch (\PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});



// Get user info from id
$app->get('/api/users/{id}', function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id');
    $sql = " SELECT * FROM users where user_id=$id";
    try{
        $db = new db();
        $pdo = $db->connect();
        $stmt = $pdo->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }catch(\PDOException $e){
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }

});