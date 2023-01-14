<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Offer;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class AllOffers extends Query
{
    protected $attributes = [
        'name' => 'allOffers',
        'description' => 'All Offers query'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Offer'));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        return Offer::select($select)->with($with)->get();
    }
}
