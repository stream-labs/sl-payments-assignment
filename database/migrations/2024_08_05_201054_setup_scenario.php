<?php

use Illuminate\Database\Migrations\Migration;
use App\Actions\RunScenario;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $action = app(RunScenario::class);
        $action->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $action = app(RunScenario::class);
        $action->teardown();
    }
};
