<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    public function render($request, Throwable $e)
    {
        if ($request->is('api/*') || $request->wantsJson()) {
            $response = $this->renderExceptionAsJson($this->prepareException($e));
        } else {
            $response = parent::render($request, $e);
        }

        return $response;
    }

    protected function prepareException(Throwable $e)
    {
        return match (true) {
            $e instanceof \DomainException => new HttpException(422, $e->getMessage(), $e),
            $e instanceof AuthenticationException => new HttpException(401, $e->getMessage(), $e),
            default => parent::prepareException($e),
        };
    }

    private function renderExceptionAsJson(Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof ValidationException => $this->renderInvalidatedAsJson($e),
            $e instanceof HttpExceptionInterface => $this->renderHttpExceptionAsJson($e),
            default => $this->renderFallback($e),
        };
    }

    protected function renderInvalidatedAsJson(ValidationException $e): JsonResponse
    {
        return response()->json([
            'status' => 'Error',
            'code' => 422,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    }

    protected function renderHttpExceptionAsJson(HttpExceptionInterface $e): JsonResponse
    {
        return response()->json([
            'status' => 'Error',
            'code' => $e->getStatusCode() ?: 500,
            'message' => $e->getMessage(),
        ], $e->getStatusCode() ?: 500);
    }

    protected function renderFallback(Throwable $e): JsonResponse
    {
        $data = [
            'status' => 'Error',
            'code' => 500,
            'message' => 'Something went wrong',
        ];

        if (!App::isProduction() && config('app.debug')) {
            $data['message'] = $e->getMessage();
            $data['file'] = $e->getFile();
            $data['line'] = $e->getLine();
            $data['context'] = $e->getTrace();
        }

        return response()->json($data, $data['code']);
    }
}
