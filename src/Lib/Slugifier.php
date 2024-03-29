<?php

namespace Marcohern\Slugifier\Lib;

use Marcohern\Slugifier\Slug;
use Marcohern\Slugifier\Lib\SlugFormatter;
use Marcohern\Slugifier\Lib\Uniquifier;

/**
 * Slug manager
 */
class Slugifier {

  /**
   * Slug Formatter
   */
  protected $formatter;

  /**
   * Slug uniquifier
   */
  protected $uniquifier;

  /**
   * Constructor
   * @param Marcohern\Slugifier\Lib\SlugFormatter $formatter Slug formatter
   * @param Marcohern\Slugifier\Lib\Uniquifier $uniquifier uniquifier
   */
  public function __construct(SlugFormatter $formatter, Uniquifier $uniquifier) {
    $this->formatter = $formatter;
    $this->uniquifier = $uniquifier;
  }

  /**
   * Set the format
   * @param string $format Slug format
   */
  public function setFormat(string $format) { $this->formatter->setFormat($format); }

  /**
   * Set the format for when the index is zero
   * @param string $format Slug format
   */
  public function setFormatIfZero(string $format) { $this->formatter->setFormatIfZero($format); }

  /**
   * Slugify a target
   * @param string $target What to slugify
   * @param string $sep separator
   * @return string slug
   */
  public function slugify(string $target, string $sep = '-') : string {
    return str_slug($target,$sep);
  }

  /**
   * Check the current slug sequence
   * @param string $source Slug source
   * @param string $entity Entity
   * @return array Slug record
   */
  public function check(string $source, string $entity='') : array {
    $slug = $this->slugify($source);
    $entity = $this->slugify($entity);
    return $this->uniquifier->checkSlug($slug, $entity);
  }
  
  /**
   * Generates a contextualized slub based on the current state of the slug.
   * Does not update the slug record.
   * @param string $slug Source Slug
   * @param string $entity Slug entity
   * @param array $slugFormats list of formats in order
   * @param array $fields names and values that will me matched with the formats.
   * @return array Slug record
   */
  public function checkContext(string $source, string $entity='', array $slugFormats=[], array $sourceFields=[]) : array {
    $slug = $this->slugify($source);
    $entity = $this->slugify($entity);
    $fields = [];
    foreach ($sourceFields as $name => $value) {
      $fields[$name] = $this->slugify($value);
    }
    return $this->uniquifier->contextualizeSlug($slug, $entity, $slugFormats, $fields);
  }

  /**
   * Slugify and store the slug
   * @param string $source Slug source
   * @param string $entity Entity source
   * @return array Slug record
   */
  public function store(string $source, string $entity='') : array {
    $slug = $this->slugify($source);
    $entity = $this->slugify($entity);
    return $this->uniquifier->storeSlug($slug, $entity);
  }

  public function storeSlug(string $source, string $entity='') : string {
    return $this->store($source, $entity)['slug'];
  }

  /**
   * Generates a contextualized slub based on the current state of the slug.
   * Then Update the slug record.
   * @param string $slug Source Slug
   * @param string $entity Slug entity
   * @param array $slugFormats list of formats in order
   * @param array $fields names and values that will me matched with the formats.
   * @return array Slug record
   */
  public function contextualize(string $source, string $entity='', array $slugFormats=[], array $sourceFields=[]) : array {
    $slug = $this->slugify($source);
    $entity = $this->slugify($entity);
    $fields = [];
    foreach ($sourceFields as $name => $value) {
      $fields[$name] = $this->slugify($value);
    }
    return $this->uniquifier->contextualizeAndStoreSlug($slug, $entity, $slugFormats, $fields);
  }
}