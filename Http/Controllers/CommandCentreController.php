<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;

class CommandCentreController extends Controller
{
    public function index()
    {
        return response(view('nexcore_client_manager::command-centre'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('X-LiteSpeed-Cache-Control', 'no-cache')
            ->header('Pragma', 'no-cache');
    }
}
