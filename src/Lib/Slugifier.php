<?php

namespace Marcohern\Slugifier\Lib;

use Marcohern\Slugifier\Slug;
use Marcohern\Slugifier\Lib\SlugFormatter;
use Marcohern\Slugifier\Lib\Uniquifier;

class Slugifier {

  protected $formatter;
  protected $uniquifier;

  public function __construct(SlugFormatter $formatter, Uniquifier $uniquifier) {
    $this->formatter = $formatter;
    $this->uniquifier = $uniquifier;
  }

  public function setFormat(string $format) { $this->formatter->setFormat($format); }
  public function setFormatIfZero(string $format) { $this->formatter->setFormatIfZero($format); }

  public function slugify(string $target, string $sep = '-') : string {
    return str_slug($target,$sep);
  }

  public function check(string $source, string $entity='') : array {
    $slug = $this->slugify($source);
    $entity = $this->slugify($entity);
    return $this->uniquifier->checkSlug($slug, $entity);
  }

  public function checkContext(string $source, string $entity='', array $slugFormats=[], array $sourceFields=[]) : array {
    $slug = $this->slugify($source);
    $entity = $this->slugify($entity);
    $fields = [];
    foreach ($sourceFields as $name => $value) {
      $fields[$name] = $this->slugify($value);
    }
    return $this->uniquifier->contextualizeSlug($slug, $entity, $slugFormats, $fields);
  }

  public function store(string $source, string $entity='') : array {
    $slug = $this->slugify($source);
    $entity = $this->slugify($entity);
    return $this->uniquifier->storeSlug($slug, $entity);
  }

  public function contextualize(string $source, string $entity='', array $slugFormats=[], array $sourceFields=[]) : array {
    $entity = $this->slugify($entity);
    $ctx = (object)$this->checkContext($source, $entity, $slugFormats, $sourceFields);
    return $this->uniquifier->storeSlug($ctx->slug, $entity);
  }
}