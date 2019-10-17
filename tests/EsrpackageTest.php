<?php

namespace Checkitsedo\Esrpackage\Test;

use Checkitsedo\Esrpackage\Esrpackage;

class EsrpackageTest extends TestCase
{
    public function testAddition()
    {
        $result = Esrpackage::add(17, 3);

        $this->assertEquals(20, $result);
    }
}
