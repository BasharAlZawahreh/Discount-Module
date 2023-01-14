<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Offer;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OfferType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Offer',
        'description' => 'Offer type',
        'model' => Offer::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::id()
            ],
            'offer_name' => [
                'type' => Type::string()
            ],
         
            'options' => [
                'type' => Type::listOf(GraphQL::type('Option'))
            ]
        ];
    }
}
