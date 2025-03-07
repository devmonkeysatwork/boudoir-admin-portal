<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            return redirect('login');
        });
        $exceptions->render(function (Throwable $e, Request $request) {
            \Illuminate\Support\Facades\Mail::to(env('SUPPORT_EMAIL'))->send(new \App\Mail\ExceptionReportEmail($e));
            return response()->view('error.server_error', status: 500);
        });
    })->create();
