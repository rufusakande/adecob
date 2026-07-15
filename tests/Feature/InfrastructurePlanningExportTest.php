<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class InfrastructurePlanningExportTest extends TestCase
{
	public function test_planned_route_is_registered()
	{
		$this->assertTrue(Route::has('infrastructures.planned'));
	}

	public function test_infrastructure_work_table_has_planification_columns()
	{
		$this->assertTrue(Schema::hasTable('infrastructure_works'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'acteurs_concernes'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'sources_financement'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'annee_execution'));
	}
}
