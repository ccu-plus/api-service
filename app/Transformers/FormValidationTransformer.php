<?php

namespace App\Transformers;

use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use League\Fractal\TransformerAbstract;

class FormValidationTransformer extends TransformerAbstract
{
    /**
     * Form validation transformer.
     *
     * @param MessageBag $bag
     *
     * @return array
     */
    public function transform(MessageBag $bag)
    {
        return array_map(function ($msg) {
            return Arr::first($msg);
        }, $bag->messages());
    }
}
