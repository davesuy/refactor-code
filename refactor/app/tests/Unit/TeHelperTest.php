<?php

namespace Tests\Unit;

use DTApi\Helpers\TeHelper;
use Carbon\Carbon;
use Tests\TestCase;

class TeHelperTest extends TestCase
{
    /**
     * Test willExpireAt function
     *
     * @return void
     */
    public function testWillExpireAt()
    {
        $due_time = Carbon::now()->addHours(3);
        $created_at = Carbon::now();

        $expected_result = $due_time->format('Y-m-d H:i:s');
        $actual_result = TeHelper::willExpireAt($due_time, $created_at);

        $this->assertEquals($expected_result, $actual_result);

        $due_time = Carbon::now()->addHours(24);
        $expected_result = $created_at->addMinutes(90)->format('Y-m-d H:i:s');
        $actual_result = TeHelper::willExpireAt($due_time, $created_at);

        $this->assertEquals($expected_result, $actual_result);

        $due_time = Carbon::now()->addHours(48);
        $expected_result = $created_at->addHours(16)->format('Y-m-d H:i:s');
        $actual_result = TeHelper::willExpireAt($due_time, $created_at);

        $this->assertEquals($expected_result, $actual_result);

        $due_time = Carbon::now()->addHours(96);
        $expected_result = $due_time->subHours(48)->format('Y-m-d H:i:s');
        $actual_result = TeHelper::willExpireAt($due_time, $created_at);

        $this->assertEquals($expected_result, $actual_result);
    }
}

