<?php

namespace Marcohern\Slugifier\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Marcohern\Slugifier\Lib\SlugFormatter;
use Marcohern\Slugifier\Lib\Uniquifier;
use Marcohern\Slugifier\Lib\Slugifier;
use Marcohern\Slugifier\Slug;

class SlugifierTest extends TestCase
{
  use RefreshDatabase;

  protected $slugifier;
  protected $formatter;
  protected $uniquifier;
  
  protected function setUp(): void {
    parent::setUp();
    $this->formatter = new SlugFormatter;
    $this->uniquifier = new Uniquifier;
    $this->slugifier = new Slugifier($this->formatter, $this->uniquifier);
  }

  protected function tearDown(): void {
    unset($this->slugifier);
    unset($this->uniquifier);
    unset($this->formatter);
    parent::tearDown();
  }

  public function test_slugify() {
    $this->assertEquals("marco-hernandez",$this->slugifier->slugify("Marco Hernandez"));
    $this->assertEquals("mcdonalds",$this->slugifier->slugify("Mc'Donalds"));
  }

  public function test_check() {
    $slug = new Slug;
    $slug->entity = 'games';
    $slug->slug = 'death-stranding';
    $slug->sequence = 5;
    $slug->save();

    $this->assertDatabaseHas('slugifier', [
      'entity' => 'games', 'slug' => 'death-stranding', 'sequence' => 5
    ]);
    
    $result = (object)$this->slugifier->check('Death Stranding','Games');
    $this->assertSame('games', $result->entity);
    $this->assertSame('death-stranding-5', $result->slug);
    $this->assertSame(5, $result->sequence);
  }

  public function test_contextualize() {
    $formats = [
      '%slug',
      '%slug-%prox',
      '%slug-%zone',
      '%slug-%prox-%city',
      '%slug-%zone-%city',
      '%slug-%prox-%zone-%city',
    ];
    $fields = [
      'prox' => 'Lleras',
      'zone' => 'Poblado',
      'city' => 'Medellin'
    ];
    
    $result = (object)$this->slugifier->contextualize('BBC', 'Bars', $formats, $fields);
    $this->assertSame('bars', $result->entity);
    $this->assertSame('bbc', $result->slug);
    $result = (object)$this->slugifier->contextualize('BBC', 'Bars', $formats, $fields);
    $this->assertSame('bbc-lleras', $result->slug);
    $result = (object)$this->slugifier->contextualize('BBC', 'Bars', $formats, $fields);
    $this->assertSame('bbc-poblado', $result->slug);
    $result = (object)$this->slugifier->contextualize('BBC', 'Bars', $formats, $fields);
    $this->assertSame('bbc-lleras-medellin', $result->slug);
    $result = (object)$this->slugifier->contextualize('BBC', 'Bars', $formats, $fields);
    $this->assertSame('bbc-poblado-medellin', $result->slug);
    $result = (object)$this->slugifier->contextualize('BBC', 'Bars', $formats, $fields);
    $this->assertSame('bbc-lleras-poblado-medellin', $result->slug);
    $result = (object)$this->slugifier->contextualize('BBC', 'Bars', $formats, $fields);
    $this->assertSame('bbc-lleras-poblado-medellin-1', $result->slug);
  }

  public function test_store() {
    $slug = new Slug;
    $slug->entity = 'games';
    $slug->slug = 'death-stranding';
    $slug->sequence = 5;
    $slug->save();

    $this->assertDatabaseHas('slugifier', [
      'entity' => 'games', 'slug' => 'death-stranding', 'sequence' => 5
    ]);
    
    $result = (object)$this->slugifier->store('Death Stranding','Games');
    $this->assertSame('games', $result->entity);
    $this->assertSame('death-stranding-5', $result->slug);
    $this->assertSame(5, $result->sequence);
    $result = (object)$this->slugifier->store('Death Stranding','Games');
    $this->assertSame('death-stranding-6', $result->slug);
    $this->assertSame(6, $result->sequence);
  }
}
