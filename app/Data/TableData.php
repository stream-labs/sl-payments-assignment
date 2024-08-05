<?php

namespace App\Data;

use Illuminate\Support\Collection;

class TableData
{
    public function __construct(public array $rows, public ?array $headers = [], public ?array $footer = [])
    {
    }
}