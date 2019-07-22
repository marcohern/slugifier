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

  public function slugify(SlugifyRequest $request) {
    $sep = $request->input('sep','-');
    return $this->slugifier->slugify($request->source, $sep);
  }

  public function check(UniquefyRequest $request) {

    $sep = $request->input('sep','-');
    $source = $this->slugifier->slugify($request->source, $sep);
    $entity = $this->slugifier->slugify($request->input('entity', ''), $sep);
    $format = $request->input('format', '%slug-%n');
    $formatIfZero = $request->input('formatIfZero', '%slug');

    return $this->slugifier->check($source, $entity, $format, $formatIfZero);
  }

  public function store(UniquefyRequest $request) {

    $sep = $request->input('sep','-');
    $source = $this->slugifier->slugify($request->source, $sep);
    $entity = $this->slugifier->slugify($request->input('entity', ''), $sep);
    $format = $request->input('format', '%slug-%n');
    $formatIfZero = $request->input('formatIfZero', '%slug');

    return $this->slugifier->store($source, $entity, $format, $formatIfZero);
  }

  public function storex(Request $request) {
    $slug = $this->slugifier->slugify($request->source);
    $entity = $request->input('entity','');
    return $this->slugifier->contextualize(
      $slug,
      $entity,
      $request->input('slugFormats',[]),
      $request->input('fields',[]),
      $request->input('format', '%slug-%n'),
      $request->input('formatIfZero', '%slug')
    );
  }
}
