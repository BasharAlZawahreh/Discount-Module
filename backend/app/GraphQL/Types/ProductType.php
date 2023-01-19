<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Product;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Product',
        'description' => 'Product type',
        'model'=>Product::class
    ];

    public function fields(): array
    {
        return [
            'id'=>[
                'type'=>Type::id()
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'name'
            ],
            'price'=>[
                'type'=>Type::float(),
                'description'=>'price'
            ],
        ];
    }
}
