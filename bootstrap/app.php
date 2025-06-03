<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // lançcar exception quando houver de atentiticacão
         $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {

                return response()->json([
                    'message' => "Nao autorizado! Você precisa estar autenticado para acessar este recurso.",
                    'success' => false,
                    'data' => null
                ], 401);
            }
        });

        $exceptions->render(function(AuthenticationException  $e){
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ]);
        });
    })->create();
