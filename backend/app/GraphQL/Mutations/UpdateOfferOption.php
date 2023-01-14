<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\OfferOption;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateOfferOption extends Mutation
{
    protected $attributes = [
        'name' => 'updateOfferOption',
        'description' => 'Update Offer Option mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Option');
    }

    public function args(): array
    {
        return [
            'id'=>[
                'type'=>Type::id(),
                'description'=>'id'
            ],
            'factor'=>[
                'type'=>Type::string(),
                'description'=>'factor'
            ],
            'status'=>[
                'type'=>Type::string()
            ],
            'percentge_value' => [
                'type' => Type::float()
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $option = OfferOption::findOrFail($args['id']);

        return $option->update([
            'factor'=>$args['factor'],
            'status'=>$args['status'],
            'percentge_value'=>$args['percentge_value'],
        ]);
    }
}
