<?php

namespace Marcohern\Slugifier\Lib;

use Marcohern\Slugifier\Lib\SlugFormatter;
use Marcohern\Slugifier\Slug;

/**
 * Makes sure sulgs are unique.
 */
class Uniquifier {

  /**
   * Marcohern\Slugifier\Lib\SlugFormatter
   */
  protected $formatter;

  /**
   * Constructor
   * @param Marcohern\Slugifier\Lib\SlugFormatter Slug formatter
   */
  public function __construct(SlugFormatter $formatter = null) {
    if (is_null($formatter)) $this->formatter = new SlugFormatter;
    else $this->formatter = $formatter;
  }

  /**
   * Check if a slug is repeated. Return the current state.
   * @param string $slug Source slug
   * @param string $entity Slug entity
   * @return array slug record
   */
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

  /**
   * Return the next state of the slug.
   * @param string $slug Source slug
   * @param string $entity Slug entity
   * @return array slug record
   */
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
  
  /**
   * Generate a unique slug using context.
   * @param string $slug Slug source
   * @param string $entity Slug entity
   * @param array $slugFormats list of formats in order
   * @param array $fields names and values that will me matched with the formats.
   * @return array unique slug record
   */
  public function contextualizeSlug(string $slug, string $entity='', array $slugFormats=[], array $fields=[]) {
    $contextSlug = null;
    $sequence = 0;
    foreach ($slugFormats as $slugFormat) {
      $contextSlug = $this->formatter->slugFormat($slug, $slugFormat, $fields);
      $slugRecord = Slug::select()->where('entity','=', $entity)->where('slug','=', $contextSlug)->first();
      if (!$slugRecord) break;
    }
    if ($slugRecord) $sequence = $slugRecord->sequence;
    $formattedSlug = $this->formatter->format($contextSlug, $sequence);
    return [
      'entity' => $entity,
      'slug' => $formattedSlug,
      'sequence' => $sequence
    ];
  }

  /**
   * Generate and store a unique slug using context.
   * @param string $slug Source Slug
   * @param string $entity Slug entity
   * @param array $slugFormats list of formats in order
   * @param array $fields names and values that will me matched with the formats.
   * @return array unique slug record
   */
  public function contextualizeAndStoreSlug(string $slug, string $entity='', array $slugFormats=[], array $fields=[]) {
    $ctx = (object)$this->contextualizeSlug($slug, $entity, $slugFormats, $fields);
    return $this->storeSlug($ctx->slug, $ctx->entity);
  }
}