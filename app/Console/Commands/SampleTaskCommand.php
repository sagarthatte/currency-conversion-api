<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SampleTaskCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sample:task';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'A sample task for testing out scheduling';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		var_dump('This is a sample string');
		return 0;
	}
}
