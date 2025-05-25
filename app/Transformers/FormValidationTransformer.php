<?php

declare(strict_types=1);

namespace App\Transformers;

use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use League\Fractal\TransformerAbstract;

class FormValidationTransformer extends TransformerAbstract
{
    /**
     * Form validation transformer.
     *
     */
    public function transform(MessageBag $bag): array
    {
        return array_map(function ($msg) {
            return Arr::first($msg);
        }, $bag->messages());
    }
}
