<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Returns a generic JSON error on login failure: 429 when throttled, 401 otherwise.
 * The message never reveals whether the email exists.
 */
class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        if ($exception instanceof TooManyLoginAttemptsAuthenticationException) {
            return $this->buildResponse(Response::HTTP_TOO_MANY_REQUESTS, 'Too Many Requests', 'Too many login attempts. Please try again later.');
        }

        return $this->buildResponse(Response::HTTP_UNAUTHORIZED, 'Unauthorized', 'Invalid credentials.');
    }

    private function buildResponse(int $code, string $result, string $msg): JsonResponse
    {
        return new JsonResponse([
            'request' => [
                'result' => $result,
                'code' => $code,
                'msg' => $msg,
            ],
            'data' => [],
        ], $code);
    }
}
