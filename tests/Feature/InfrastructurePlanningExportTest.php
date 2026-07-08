<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class InfrastructurePlanningExportTest extends TestCase
{
	public function test_planned_route_is_registered()
	{
		$this->assertTrue(Route::has('infrastructures.planned'));
	}
}
