<?php

namespace App\Service\Shared;

use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiResponseService
{
    public array $defaultStatus = ['result' => 'success', 'msg' => ''];

    public function __construct(private readonly RequestStack $request) {}

    public function getApiResponse(int $code, ?array $status = [], ?array $data = []): JsonResponse
    {
        if (count($status) === 0) {
            $status = $this->defaultStatus;
        }

        $datetime = (new DateTime())->format('Y-m-d H:i:s');
        $response['request'] = [
            'result' => $status['result'],
            'code' => $code,
            'msg' => $status['msg'],
        ];

        if (in_array($_ENV['APP_ENV'], ['dev', 'test'])) {
            $response['request'] = [
                'result' => $status['result'],
                'code' => $code,
                'msg' => $status['msg'],
                'datetime' => $datetime,
                'uri' => $this->request->getCurrentRequest()->getRequestUri(),
                'method' => $this->request->getCurrentRequest()->getMethod(),
                'from' => $_SERVER['HTTP_HOST'],
                'file' => $status['file'] ?? '',
                'line' => $status['line'] ?? '',
            ];
        }

        $response['data'] = $data;

        return new JsonResponse($response);
    }
}
