<?php

namespace Marcohern\Slugifier\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Marcohern\Slugifier\Lib\SlugFormatter;
use Marcohern\Slugifier\Lib\Uniquifier;
use Marcohern\Slugifier\Slug;

class UniquifierTest extends TestCase
{
  use RefreshDatabase;
  
  protected function setUp(): void {
    parent::setUp();
  }

  protected function tearDown(): void {
    parent::tearDown();
  }

  public function test_checkSlug() {
    $sr = new Slug;
    $sr->entity = 'actors';
    $sr->slug = 'brad-pitt';
    $sr->sequence = 12;
    $sr->save();

    $slugifier = new Uniquifier;
    $result = $slugifier->checkSlug('brad-pitt', 'actors');
    $this->assertEquals($result, ['entity' => 'actors','slug' => 'brad-pitt-12','sequence' => 12]);
  }

  public function test_storeSlug() {
    $sr = new Slug;
    $sr->entity = 'actors';
    $sr->slug = 'brad-pitt';
    $sr->sequence = 5;
    $sr->save();

    $slugifier = new Uniquifier;
    $result = $slugifier->storeSlug('brad-pitt', 'actors');
    $this->assertEquals($result, ['entity' => 'actors','slug' => 'brad-pitt-5','sequence' => 5]);

    $sr = Slug::select()->where('entity','=', 'actors')->where('slug', '=', 'brad-pitt')->first();
    $this->assertEquals($sr->sequence, 6);
  }

  public function test_contextualizeSlug() {
    $fields = [
      'prox' => 'andino',
      'city' => 'bogota',
      'zone' => 'chico'
    ];

    $formats = [
      '%slug',
      '%slug-%prox',
      '%slug-%zone',
      '%slug-%prox-%city',
      '%slug-%zone-%city',
      '%slug-%prox-%zone-%city',
    ];

    $slugifier = new Uniquifier;
    $r1 = (object)$slugifier->contextualizeSlug('bbc', 'bars', $formats, $fields);
    $r2 = (object)$slugifier->contextualizeSlug('bbc', 'bars', $formats, $fields);
    $r3 = (object)$slugifier->contextualizeSlug('bbc', 'bars', $formats, $fields);
    $r4 = (object)$slugifier->contextualizeSlug('bbc', 'bars', $formats, $fields);
    $r5 = (object)$slugifier->contextualizeSlug('bbc', 'bars', $formats, $fields);
    $r6 = (object)$slugifier->contextualizeSlug('bbc', 'bars', $formats, $fields);

    $this->assertEquals($r1->slug, 'bbc');
    $this->assertEquals($r2->slug, 'bbc-andino');
    $this->assertEquals($r3->slug, 'bbc-chico');
    $this->assertEquals($r4->slug, 'bbc-andino-bogota');
    $this->assertEquals($r5->slug, 'bbc-chico-bogota');
    $this->assertEquals($r6->slug, 'bbc-andino-chico-bogota');
  }
}
