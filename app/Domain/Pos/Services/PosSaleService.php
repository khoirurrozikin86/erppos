<?php

namespace App\Domain\Pos\Services;

use App\Domain\Pos\Actions\CreatePosSaleAction;
use App\Models\PosSale;

class PosSaleService
{
    public function __construct(private CreatePosSaleAction $create) {}
    public function create(array $data, int $userId): PosSale { return ($this->create)($data, $userId); }
}
