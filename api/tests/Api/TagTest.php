<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class TagTest extends ApiTestCase
{

  private $authAdmin = [ 'X-AUTH-EMAIL' => 'unitTest', 'X-AUTH-ROLES' => 'OPS_ADMIN' ];
  private $authUser  = [ 'X-AUTH-EMAIL' => 'unitTest', 'X-AUTH-ROLES' => 'OPS_READONLY' ];

  /**
   * @dataProvider dataProviderCollection
   **/
  public function testCollection(array $context, array $expected): void
  {
    $response = static::createClient()->request($context['method'], $context['path'], [ 'headers' => $context['headers'], 'json' => $context['json'] ]);

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
    $context = [];
    $expected = [];

    $context['path'] = '/api/tags';
    $context['headers'] = $this->authAdmin;

    $context['method'] = 'GET';
    $context['json'] = null;
    $expected['success'] = true;
    $expected['json'] = ['@type' => 'hydra:Collection'];
    yield 'test_get_collection' => [$context, $expected];

    $context['method'] = 'POST';
    $context['json'] = [ 'name' => 'test' ];
    $expected['json'] = array_merge(['@type' => 'Tag'], $context['json']);
    yield 'test_post' => [$context, $expected];

    $context['path'] = '/api/tags/test';
    $context['method'] = 'PUT';
    $context['json'] = [ 'name' => 'test-put' ];
    $expected['json'] = array_merge(['@type' => 'Tag', 'name' => 'test-put'], $context['json']);
    yield 'test_put' => [$context, $expected];

    $context['path'] = '/api/tags';
    $context['method'] = 'POST';
    $context['json'] = [ 'name' => 'test-put' ];
    $expected['json'] = [];
    $expected['success'] = false;
    yield 'test_name_unicity' => [$context, $expected];

    $context['path'] = '/api/tags/test-put';
    $context['method'] = 'DELETE';
    $context['json'] = null;
    $expected['json'] = null;
    $expected['success'] = true;
    yield 'test_delete' => [$context, $expected];

  }
}
