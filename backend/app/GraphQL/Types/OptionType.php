<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\OfferOption;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OptionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Option',
        'description' => 'An Option type',
        'model'=>OfferOption::class
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
            'factor'=>[
                'type'=>Type::string(),
                'description'=>'factor'
            ],
            'status'=>[
                'type'=>Type::string()
            ],
            'percentge_value' => [
                'type' => Type::getNullableType(Type::float())
            ],
            'offer'=>[
                'type'=>GraphQL::type('Offer'),
                'description'=>'factor'
            ]
        ];
    }
}
