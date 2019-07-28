<?php

namespace Marcohern\Slugifier\Lib;

use Marcohern\Slugifier\Lib\SlugFormatter;
use Marcohern\Slugifier\Slug;

class BaseSlugifier {

  protected $formatter;

  public function __construct() {
    $this->formatter = new SlugFormatter;
  }

  public function checkSlug(string $slug, string $entity='') {
    $slugRecord = Slug::select()->where('entity','=', $entity)->where('slug','=', $slug)->first();
    if (!$slugRecord) $sequence = 0;
    else $sequence = $slugRecord->sequence;
    $slugFormatted = $this->formatter->format($slug, $sequence);
    return [
      'entity' => $entity,
      'slug' => $slugFormatted,
      'sequence' => $sequence
    ];
  }

  public function storeSlug(string $slug, string $entity='') {
    $slugRecord = Slug::select()->where('entity','=', $entity)->where('slug','=', $slug)->first();
    $sequence = 0;
    if ($slugRecord) {
      $sequence = $slugRecord->sequence;
      $slugRecord->sequence++;
    } else {
      $slugRecord = new Slug;
      $slugRecord->entity = $entity;
      $slugRecord->slug = $slug;
      $slugRecord->sequence = $sequence+1;
    }
    $slugRecord->save();
    $formattedSlug = $this->formatter->format($slug, $sequence);
    return [
      'entity' => $entity,
      'slug' => $formattedSlug,
      'sequence' => $sequence
    ];
  }

  public function contextualizeSlug(string $slug, string $entity='', array $slugFormats=[], array $fields=[]) {
    $contextSlug = null;
    foreach ($slugFormats as $slugFormat) {
      $contextSlug = $this->formatter->slugFormat($slug, $slugFormat, $fields);
      $slugRecord = Slug::select()->where('entity','=', $entity)->where('slug','=', $contextSlug)->first();
      if (!$slugRecord) break;
    }
    return $this->storeSlug($contextSlug, $entity);
  }
}