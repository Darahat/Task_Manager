<?php

namespace Modules\TaskManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Test 404 error page
     */
    public function test404()
    {
        abort(404);
    }

    /**
     * Test 500 error page
     */
    public function test500()
    {
        abort(500);
    }

    /**
     * Test 403 error page
     */
    public function test403()
    {
        abort(403);
    }

    /**
     * Test 503 error page
     */
    public function test503()
    {
        abort(503);
    }
}
