<?php

declare(strict_types=1);

namespace App\Tests\Concern;

use App\Foundation\Util\Json;

trait WithUtils
{
    protected Json $json;

    protected function setUpUtils(): void
    {
        $this->json = new Json();
    }
}