<?php

namespace App\Models;

use App\Observers\CartObserver;
use App\traits\HasCookieId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;

class Cart extends Model
{
    use HasFactory;
    protected $guarded =[];

    public static function booted()
    {
        static::addGlobalScope(function(Builder $builder){
            $builder->where('cookie_id',static::getCookieId());
        });

        static::creating(function(Cart $cart){
            $cart->cart_id = self::getCookieId();
        });
    }

    public static function getCookieId()
    {
        $cookie_id = Cookie::get('cart_cookie_id');
        if (!$cookie_id) {
            $cookie_id = \Str::uuid();
            Cookie::queue('cart_cookie_id', $cookie_id, 30 * 24 * 60);
        }
        return $cookie_id;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
