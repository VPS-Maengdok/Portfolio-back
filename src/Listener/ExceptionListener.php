<?php

namespace App\Listener;

use App\Service\Shared\ApiResponseService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function __construct(private ApiResponseService $apiResponse) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        $status = [
            'result' => Response::$statusTexts[$code],
            'msg' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];

        $response = $this->apiResponse->getApiResponse($code, $status);

        if ($exception instanceof HttpExceptionInterface) {
            foreach ($exception->getHeaders() as $message => $value) {
                $response->headers->set($message, $value);
            }
        }

        $event->setResponse($response);
    }
}