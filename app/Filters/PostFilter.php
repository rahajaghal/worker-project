<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class PostFilter
{
    public function filter()
    {
         return[
            'content',
            'price',
            'worker.name',
            AllowedFilter::callback('item', function (Builder $query, $value) {
//                (new PostFilter())->filter($query, $value);
                                     $query->where('price','like',"%{$value}%")
                                     ->orWhere('content','like',"%{$value}%")
                                     ->orWhereHas('worker',function (Builder $query) use ($value){
                                         $query->where('name','like',"%{$value}%");
                                     });
            })
        ];
    }
}
