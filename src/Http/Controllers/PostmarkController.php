<?php

namespace RickDBCN\FilamentEmail\Http\Controllers;

use Illuminate\Routing\Controller;

class PostmarkController extends Controller
{
    public function __construct()
    {
        $this->middleware('PostmarkMiddleware');
    }

    public function handle()
    {
        dd('test');
    }
}
