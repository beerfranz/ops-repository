<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class OperationTest extends ApiTestCase
{
  protected $itemId = null;

  /**
   * @dataProvider dataProviderCollection
   **/
  public function testCollection(array $context, array $expected): void
  {
    $response = static::createClient()->request($context['method'], $context['path'], [ 'json' => $context['json'] ]);

    if (isset($expected['success']))
    {
      if ($expected['success'])
        $this->assertResponseIsSuccessful();
      else
        $this->assertResponseIsNotSuccessful();
    }

    if (isset($expected['json']))
      $this->assertJsonContains($expected['json']);

    if ($this->itemId === null && $context['method'] === 'POST')
    {
      $content = json_decode($response->getContent());
      $this->itemId = $content->id;
    }
    
  }

  /**
   * @dataProvider dataProviderCollection
   **/
  public function dataProviderCollection(): iterable
  {
    $context = [];
    $expected = [];

    $context['path'] = '/api/operations';

    $context['method'] = 'GET';
    $context['json'] = null;
    $expected['success'] = true;
    $expected['json'] = ['@type' => 'hydra:Collection'];
    yield 'test_get_collection' => [$context, $expected];

    $context['method'] = 'POST';
    $context['json'] = [ 'name' => 'test', 'startedAt' => '2022-11-13T01:49:00+00:00' ];
    $expected['json'] = array_merge(['@type' => 'Operation'], $context['json']);
    yield 'test_post' => [$context, $expected];

    $context['path'] = '/api/operations/10';

    $context['method'] = 'PUT';
    $context['json'] = [ 'name' => 'test-put', 'tags' => [ 'tag1', 'tag2' ] ];
    $expected['json'] = array_merge(['@type' => 'Operation'], $context['json']);
    yield 'test_put' => [$context, $expected];

    $context['method'] = 'DELETE';
    $context['json'] = null;
    $expected['json'] = null;
    yield 'test_delete' => [$context, $expected];

  }
}
