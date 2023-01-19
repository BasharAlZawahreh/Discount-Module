<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Symfony\Component\Intl\Countries;

use function PHPSTORM_META\map;

class AllCountries extends Query
{
    protected $attributes = [
        'name' => 'allCountries',
        'description' => 'All Countries query'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Json'));
    }

    public function resolve()
    {
        $countries = Countries::getNames();
        $cc = [];

        foreach ($countries as $key => $value) {
            $cc[] = [
                'code' => $key,
                'name' => $value
            ];
        }

        return $cc;
    }
}
