<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reports', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->string('type');
			$table->string('currency');
			$table->string('range');
			$table->string('interval');
			$table->text('response_data')->nullable();
			$table->enum('status', ['pending', 'completed']);
			$table->datetime('request_at');
			$table->datetime('response_at')->nullable();
			$table->timestamps();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('reports');
	}
};
