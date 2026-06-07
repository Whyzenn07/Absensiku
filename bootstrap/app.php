<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // TEMP DEBUG: return plain-text error so we can see it in curl output
        $exceptions->render(function (\Throwable $e, $request) {
            error_log('[[ERR]] ' . get_class($e) . ': ' . $e->getMessage()
                . ' @ ' . basename($e->getFile()) . ':' . $e->getLine());
            return response(
                '[[DEBUG500]] ' . get_class($e) . "\n"
                    . $e->getMessage() . "\n"
                    . $e->getFile() . ':' . $e->getLine() . "\n"
                    . substr($e->getTraceAsString(), 0, 3000),
                500,
                ['Content-Type' => 'text/plain']
            );
        });
    })->create();
