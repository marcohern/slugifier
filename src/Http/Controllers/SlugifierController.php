<?php

namespace Marcohern\Slugifier\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Marcohern\Slugifier\Slug;
use Marcohern\Slugifier\Lib\Slugifier;
use Marcohern\Slugifier\Http\Requests\SlugifyRequest;
use Marcohern\Slugifier\Http\Requests\UniquefyRequest;

class SlugifierController extends Controller
{
  protected $slugifier;

  public function __construct(Slugifier $slugifier) {
    $this->slugifier = $slugifier;
  }

  public function index() {
    return Slug::all();
  }

  public function slugify(SlugifyRequest $request) {
    $sep = $request->input('sep','-');
    return $this->slugifier->slugify($request->source, $sep);
  }

  public function check(UniquefyRequest $request) {
    return $this->slugifier->check($request->source, $request->entity);
  }

  public function store(UniquefyRequest $request) {
    return $this->slugifier->store($request->source, $request->entity);
  }

  public function storex(Request $request) {
    return $this->slugifier->contextualize(
      $request->source, $request->entity,
      $request->slugFormats, $request->fields
    );
  }
}
