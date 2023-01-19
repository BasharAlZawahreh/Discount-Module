<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class JsonType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Json',
        'description' => 'Json type'
    ];

    public function fields(): array
    {
        return [
            'code' => [
                'type' => Type::string(),
            ],
            'name' => [
                'type' => Type::string()
            ]
        ];
    }
}
