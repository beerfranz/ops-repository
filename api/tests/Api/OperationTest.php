<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class OperationTest extends ApiTestCase
{
  private $operation_path = '/api/operations';
  private $operation_date = '2022-11-13T01:49:00+00:00';
  private $operation_tags = 'operationTest';

  /**
   * @dataProvider dataProviderCollection
   **/
  public function testCollection(array $context, array $expected): void
  {
    $path = $context['path'];
    unset($context['path']);

    $method = $context['method'];
    unset($context['method']);

    // If request on item, get an item ID
    if (in_array($method, [ 'DELETE' ]))
    {
      $response = static::createClient()->request('GET', $this->operation_path, [ 'query' => [ 'tags ' => $this->operation_tags ]]);
      $content = $response->toArray();
      $id = $content['hydra:member'][0]['id'];
      $path = str_replace('##RANDOM_ID##', $id, $path);
    }

    $response = static::createClient()->request($method, $path, $context);

    if (isset($expected['success']))
    {
      if ($expected['success'])
        $this->assertResponseIsSuccessful();
      else
        $this->assertJsonContains([ '@type' => 'hydra:Error']);
    }

    if (isset($expected['json']))
      $this->assertJsonContains($expected['json']);

  }

  /**
   * @dataProvider dataProviderCollection
   **/
  public function dataProviderCollection(): iterable
  {
    $context = [
      'method'  => 'POST',
      'path'    => $this->operation_path,
      'json'    => [ 'name' => 'test', 'startedAt' => $this->operation_date, 'tags' => [ $this->operation_tags ] ],
    ];
    $expected = [
      'success' => true,
      'json'    => array_merge(['@type' => 'Operation'], $context['json']),
    ];
    yield 'test_post' => [$context, $expected];

    $context = [
      'method'  => 'GET',
      'path'    => $this->operation_path,
      'query'    => [ 'tags' => 'operationTest' ],
    ];
    $expected = [
      'success' => true,
      'json'    => array_merge(['@type' => 'hydra:Collection'], [ 'hydra:member' => [0 => [ 'tags' => [ 'operationTest' ]] ] ]),
    ];
    yield 'test_get_collection' => [$context, $expected];

    $context = [
      'method'  => 'GET',
      'path'    => $this->operation_path,
      'query'    => [ 'from' => $this->operation_date, 'to' => $this->operation_date ],
    ];
    $expected = [
      'success' => true,
      'json'    => array_merge(['@type' => 'hydra:Collection'], [ 'hydra:member' => [0 => [ 'tags' => [ 'operationTest' ]] ] ]),
    ];
    yield 'test_get_collection_with_date_filters' => [$context, $expected];

    $context = [
      'method'  => 'DELETE',
      'path'    => $this->operation_path . '/##RANDOM_ID##',
    ];
    $expected = [
      'success' => true,
    ];
    yield 'test_delete' => [$context, $expected];

  }
}
