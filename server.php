<?php

$scriptInvokedFromCli =
    isset($_SERVER['argv'][0]) && $_SERVER['argv'][0] === 'server.php';
if($scriptInvokedFromCli) {
    $port = getenv('PORT');
    if (empty($port)) {
        $port = "3000";
    }
    echo 'starting server on port '. $port . PHP_EOL;
    exec('php -S localhost:'. $port . ' -t public_html server.php');
} else {
    return routeRequest();
}
function routeRequest()
{
    $comments = file_get_contents('./public_html/api/comments.json');
    $uri = $_SERVER['REQUEST_URI'];
    if ($uri == '/') {
        echo file_get_contents('./public_html/index.html');
    } elseif (preg_match('/\/api\/comments(\?.*)?/', $uri)) {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentsDecoded = json_decode($comments, true);
            $commentsDecoded[] = [
                'id'      => round(microtime(true) * 1000),
                'author'  => $_POST['author'],
                'text'    => $_POST['text']
            ];
            $comments = json_encode($commentsDecoded, JSON_PRETTY_PRINT);
            file_put_contents('./public_html/api/comments.json', $comments);
        }
        header('Content-Type: application/json');
        header('Cache-Control: no-cache');
        header('Access-Control-Allow-Origin: *');
        echo $comments;
    } else {
        return false;
    }
}
