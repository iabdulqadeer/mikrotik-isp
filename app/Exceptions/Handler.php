<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        // Render everything through our pretty error page
        $this->renderable(function (Throwable $e, $request) {
            [$code, $title, $desc, $debug] = $this->mapException($e);

            // In local (and APP_DEBUG=true), you may prefer the default Whoops page:
            if (config('app.debug') && app()->environment('local')) {
                // Comment the next line to always use our custom page even in local
                // return null;
            }

            return response()->view('errors.generic', [
                'code'        => $code,
                'title'       => $title,
                'description' => $desc,
                // keep debug minimal to avoid leaking sensitive info
                'debug'       => app()->environment('local') ? $debug : null,
            ], $code);
        });
    }

    /**
     * Map an exception to [code, title, description, debug]
     */
    protected function mapException(Throwable $e): array
    {
        // Defaults
        $code = 500;
        $title = 'Server Error';
        $desc = 'Something went wrong on our end. Please try again later.';
        $debug = $e->getMessage();

        // Specific types
        if ($e instanceof NotFoundHttpException) {
            $code = 404;
            $title = 'Page Not Found';
            $desc = 'We’re sorry, the page you requested could not be found. Please go back to the homepage.';
        } elseif ($e instanceof AuthenticationException) {
            $code = 401;
            $title = 'Unauthorized';
            $desc = 'You need to sign in to access this page.';
        } elseif ($e instanceof AuthorizationException) {
            $code = 403;
            $title = 'Forbidden';
            $desc = 'You don’t have permission to view this resource.';
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $code = 405;
            $title = 'Method Not Allowed';
            $desc = 'The HTTP method is not allowed for this route.';
        } elseif ($e instanceof TokenMismatchException) {
            $code = 419;
            $title = 'Page Expired';
            $desc = 'Your session has expired. Please refresh the page and try again.';
        } elseif ($e instanceof ValidationException) {
            $code = 422;
            $title = 'Validation Error';
            $desc = 'We couldn’t process your request due to validation errors.';
        } elseif ($e instanceof ThrottleRequestsException) {
            $code = 429;
            $title = 'Too Many Requests';
            $desc = 'You’ve hit the rate limit. Please slow down and try again shortly.';
        } elseif ($e instanceof HttpExceptionInterface) {
            // Any other HTTP exception with its own status
            $code = $e->getStatusCode();
            [$title, $desc] = $this->metaForStatus($code);
        }

        // Maintenance mode (503) is often thrown as HttpExceptionInterface above.
        // You can add more mappings here if needed.

        return [$code, $title, $desc, $debug];
    }

    /**
     * Titles/descriptions for common codes.
     */
    protected function metaForStatus(int $code): array
    {
        return match ($code) {
            400 => ['Bad Request', 'The request could not be understood by the server. Please check your input and try again.'],
            401 => ['Unauthorized', 'You need to sign in to access this page.'],
            403 => ['Forbidden', 'You don’t have permission to view this resource.'],
            404 => ['Page Not Found', 'We’re sorry, the page you requested could not be found. Please go back to the homepage.'],
            405 => ['Method Not Allowed', 'The HTTP method is not allowed for this route.'],
            408 => ['Request Timeout', 'The server timed out waiting for the request. Please try again.'],
            419 => ['Page Expired', 'Your session has expired. Please refresh the page and try again.'],
            422 => ['Validation Error', 'We couldn’t process your request due to validation errors.'],
            429 => ['Too Many Requests', 'You’ve hit the rate limit. Please slow down and try again shortly.'],
            500 => ['Server Error', 'Something went wrong on our end. Please try again later.'],
            503 => ['Service Unavailable', 'We’re performing maintenance or the service is temporarily unavailable.'],
            default => ['Error', 'An unexpected error occurred. Please try again later.'],
        };
    }
}
