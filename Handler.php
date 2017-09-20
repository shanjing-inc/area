<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Router;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        if (method_exists($e, 'report')) {
            return $e->report();
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $e; // throw the original exception
        }

        $context = $e instanceof OAuthServerException ?
            ['exception' => $e] : array_merge($this->context(), ['exception' => $e]);

        $logger->error($e->getMessage(), $context);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::prepareResponse($request, $response);
        } elseif ($e instanceof Responsable) {
            return $e->toResponse($request);
        }

        $e = $this->prepareException($e);

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->handlingAuthenticationExceptionResponse();
        } elseif ($e instanceof ValidationException) {
            return $this->handlingValidationExceptionToResponse($e);
        } elseif($e instanceof OAuthServerException){
            return $this->handlingOAuthServerExceptionToResponse($e);
        }

        return $this->handlingOtherExceptionToResponse($e);
    }

    protected function handlingAuthenticationExceptionResponse()
    {
        return response()->json([
            'error' => 'Unauthenticated',
            'message' => 'there is no authentication',
        ], 401);
    }

    protected function handlingValidationExceptionToResponse(ValidationException $e)
    {
        return response()->json([
            'error' => studly_case($e->validator->errors()->first()),
            'message' => $e->getMessage(),
        ], $e->status);
    }

    protected function handlingOAuthServerExceptionToResponse(OAuthServerException $e)
    {
        return response()->json([
            'error' => studly_case($e->getErrorType()),
            'message' => $e->getMessage(),
        ], $e->getHttpStatusCode());
    }


    protected function handlingOtherExceptionToResponse($e)
    {
        $status = $this->isHttpException($e) ? $e->getStatusCode() : 500;

        $headers = $this->isHttpException($e) ? $e->getHeaders() : [];

        return response()->json(
            $this->convertExceptionToArray($e), $status, $headers,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    protected function convertExceptionToArray(Exception $e)
    {
        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
        ] : [
            'error' => $this->isHttpException($e) ? studly_case($e->getMessage()) : 'ServerError',
            'message' => $this->isHttpException($e) ? $e->getMessage() . '.' : 'Server Error.',
        ];
    }
}
