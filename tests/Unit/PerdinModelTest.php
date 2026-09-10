<?php

namespace Tests\Unit;

use App\Models\Perdin;
use PHPUnit\Framework\TestCase;

class PerdinModelTest extends TestCase
{
    public function test_model_class_exists_and_has_relations(): void
    {
        $this->assertTrue(class_exists(Perdin::class));
        $this->assertTrue(method_exists(Perdin::class, 'travelers'));
        $this->assertTrue(method_exists(Perdin::class, 'rincianItems'));
        $this->assertTrue(method_exists(Perdin::class, 'dprItems'));
    }
}
