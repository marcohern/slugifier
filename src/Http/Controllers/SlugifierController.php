<?php

namespace Marcohern\Slugifier\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Marcohern\Slugifier\Slug;
use Marcohern\Slugifier\Lib\Slugifier;

class SlugifierController extends Controller
{
  protected $slugifier;

  public function __construct(Slugifier $slugifier) {
    $this->slugifier = $slugifier;
  }

  public function slugify(Request $request) {
    return $this->slugifier->slugify($request->slug, $request->sep);
  }

  public function check(Request $request, string $entity, string $slug) {
    return $this->slugifier->check($slug, $entity, $request->format, $request->formatIfZero);
  }

  public function check_global(string $slug) {
    return $this->slugifier->check($slug, '', $request->format, $request->formatIfZero);
  }

  public function store(Request $request, string $entity, string $slug) {
    return $this->slugifier->store($slug, $entity, $request->format, $request->formatIfZero);
  }

  public function store_global(Request $request, string $slug) {
    return $this->slugifier->store($slug, '', $request->format, $request->formatIfZero);
  }

  public function storex(Request $request, string $entity, string $slug) {
    return [
      'slug'=>$slug,
      'entity'=>$entity,
      'slugFormats'=>$request->slugFormats,
      'fields'=>$request->fields,
      'format'=>$request->format,
      'formatIfZero'=>$request->formatIfZero
    ];
    return $this->slugifier->storeWithContext(
      $slug, $entity, $request->slugFormats, $request->fields, $request->format, $request->formatIfZero
    );
  }

  public function storex_global(Request $request, string $slug) {
    return $this->slugifier->storeWithContext(
      $slug, '', $request->slugFormats, $request->fields, $request->format, $request->formatIfZero
    );
  }
}
