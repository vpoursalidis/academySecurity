<?php

declare(strict_types=1);

namespace Epignosis\Academy\Security;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Ex02Injection extends Ex01CSRF
{
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // ;DROP DATABASE master
        $query = "DELETE FROM users WHERE id=". $args['id'];
        // $pdo->exec($query);

        $queries = explode(';', $query);

        var_dump($queries);
        exit;

        //
        // Alternatives
        //
        // ORM (for example Laravel Eloquent):
        // $user = User::find($args['id']);
        // $user->delete();
        //
        // PDO prepared statements:
        // $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        // $stmt->bindParam(1, $args['id'], PDO::PARAM_INT);
        // $stmt->execute();
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $counter = 1;
        $rows = '';
        foreach ($this->users as $user){
            $rows .= <<<EOT
    <tr>
      <th scope="row">{$counter}</th>
      <td>{$user[0]}</td>
      <td style="width: 150px"><a class="btn btn-danger" href="del/{$counter}" role="button"><i class="bi-trash"></i> Delete</a></td>
    </tr>
EOT;
            $counter++;
        }

        $content = <<<EOT
<div class="alert alert-success" role="alert">
  Legitimate use of the App
</div>
<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Email</th>
      <th scope="col"></th>
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
