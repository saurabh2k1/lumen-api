<?php

namespace App\Http\Controllers;

use App\ValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\Study;

class StudyController extends Controller
{
    use ValidationTrait;

    public function getStudies()
    {
        return response()->json(Study::with('sites')->get());
    }
}