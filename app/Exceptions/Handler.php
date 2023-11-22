<?php

namespace App\Exceptions;

use App\Models\Product;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {

            return response()->json([
                'status' => false,
                'data' => null, 'message' => 'Id not found'
            ], 404);
        }
        if ($e instanceof InvalidRoleActionException) {

            return response()->json([
                'status' => false,
                'data' => null, 'message' => 'You can not perform this action on the requested role'
            ], 403);
        }

        return parent::render($request, $e);
    }
}
