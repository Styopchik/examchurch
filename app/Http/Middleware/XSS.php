<?php

namespace App\Http\Middleware;

use Closure;

class XSS
{
    public function handle($request, Closure $next)
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        }
        if (\Auth::check()) {
            \App::setLocale(\Auth::user()->lang);
        }
        $input = $request->all();
        array_walk_recursive($input, function (&$input) {
            $input = strip_tags($input);
        });
        $request->merge($input);
        return $next($request);
    }
}
