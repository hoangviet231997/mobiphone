<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExceptionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app('Dingo\Api\Exception\Handler')->register(function (\Dingo\Api\Exception\ResourceException $exception) {
            $errors = $exception->getErrors();
            $data   = (object)[
                'message'      => $errors->first(),
                'status_code'  => 422
            ];

            return response()->json($data, 422);
        });

        app(\Dingo\Api\Exception\Handler::class)->register(function (\Exception $exception) {
            if (!env('API_DEBUG') && !$exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $data   = (object)[
                    'message'      => $exception->getMessage(),
                    'status_code'  => 500,
                ];
                return response()->json($data, 500);
            }
        });
    }
}
