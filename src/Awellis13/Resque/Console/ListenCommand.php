<?php namespace Awellis13\Resque\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;
use Resque;
use Resque_Worker;
use Resque_Log;

/**
 * Class ResqueQueue
 *
 * @package Resque\Console
 */
class ListenCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'resque:listen';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run a resque worker';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// Read input
		$logLevel = $this->input->getOption('verbose') ? true : false;
		$queue = $this->input->getOption('queue');
		$interval = $this->input->getOption('interval');

		// Connect to redis
		Resque::setBackend(Config::get('database.redis.default.host').':'.Config::get('database.redis.default.port'),Config::get('database.redis.default.database'));

		// Launch worker
		$queues = explode(',', $queue);
		$logger = new Resque_Log($logLevel);
		$worker = new Resque_Worker($queues);
		$worker->setLogger($logger);
		$worker->logLevel = $logLevel;

		fwrite(STDOUT, '*** Starting worker '.$worker."\n");
		$worker->work($interval);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['queue', NULL, InputOption::VALUE_OPTIONAL, 'The queue to listen on', 'default'],
			['interval', NULL, InputOption::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 5],
		];
	}

} // End ListenCommand
