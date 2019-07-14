<?php

namespace Marcohern\Slugifier\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SlugifierController extends Controller
{
    public function index() {
      return ['success' => true];
    }
}
