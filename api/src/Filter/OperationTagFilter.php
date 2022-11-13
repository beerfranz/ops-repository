<?php

namespace App\Filter;

use ApiPlatform\Api\FilterInterface;
use Symfony\Component\PropertyInfo\Type;

class OperationTagFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'tags' => [
                'property' => null,
                'type' => Type::BUILTIN_TYPE_ARRAY,
                'is_collection' => true,
                'required' => false,
                'openapi' => [
                    'description' => 'Tags',
                    'explode' => false,
                ],
            ]
        ];
    }

}