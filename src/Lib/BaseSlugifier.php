<?php

namespace Marcohern\Slugifier\Lib;

use Marcohern\Slugifier\Lib\SlugFormatter;

abstract class BaseSlugifier {

  protected $formatter;

  abstract public function checkSlug(string $slug, string $entity='', string $format='%slug-%n', string $formatIfZero='%slug');
  abstract public function storeSlug(string $slug, string $entity='', string $format='%slug-%n', string $formatIfZero='%slug');
  abstract public function contextualizeSlug(
    string $slug, string $entity='',
    array $slugFormats=[], array $fields=[],
    string $format='%slug-%n', string $formatIfZero='%slug'
  );
}