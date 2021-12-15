<?php

declare(strict_types=1);

namespace Epignosis\Academy\Security;

use Psr\Http\Message\ResponseInterface;

class Helper
{
    public static function redirect(ResponseInterface $response, $path): ResponseInterface
    {
        $domain = $_SERVER['HTTP_HOST'];

        return $response
            ->withHeader('Location', 'http://'.$domain.$path)
            ->withStatus(302);
    }

    public static function generateToken(int $size): string
    {
        $bytes = random_bytes($size);
        return bin2hex($bytes);
    }

    public static function checkCSRFToken(): void
    {
        $valid = false;

        if (isset($_SESSION) && isset($_SESSION['csrf_token'])) {
            if (isset($_GET['csrf_token'])) {
                if (hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
                    $valid = true;
                }
            }
        }

        if (!$valid) {
            throw new \RuntimeException("CSRF token validation failed!");
        }
    }
}