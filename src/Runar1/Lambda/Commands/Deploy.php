<?php 

namespace Runar1\Lambda\Commands;

use Aws\Lambda\LambdaClient;

use Illuminate\Console\Command;

class Deploy extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'lambda:deploy';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Deploys the project to a lambda function on AWS.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		if (!$this->createZipFile()) {
			throw new \Exception('Failed creating zip file.');
		}
		$data = file_get_contents(storage_path('app/app.zip'));
		$client = LambdaClient::factory([
			'key'		=> env('LAMBDA_KEY'),
			'secret'	=> env('LAMBDA_SECRET'),
			'region'	=> env('LAMBDA_REGION'),
		]);
		$response = $client->updateFunctionCode([
			'FunctionName' 	=> env('LAMBDA_NAME'),
			'ZipFile'		=> base64_encode($data),
			'Publish'		=> true,
		]);
		var_dump($response);
	}

	private function createZipFile() {
		if (!extension_loaded('zip')) {
			return false;
		}
		$zip = new \ZipArchive();
		if (!$zip->open(storage_path('app/app.zip'), \ZIPARCHIVE::CREATE)) {
			return false;
		}
		$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('.'), \RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $file) {
			$file = str_replace('\\', '/', $file);
			$parts = explode('/', $file);
			if (in_array($parts[count($parts) - 1], ['app.zip', '.', '..', '.env'])) {
				continue;
			}
			if (in_array('.git', $parts)) {
				continue;
			}
			if (is_dir($file) === true) {
				$zip->addEmptyDir(str_replace('./', '', $file) . '/');
			} else if (is_file($file) === true) {
				$zip->addFromString(str_replace('./', '', $file), file_get_contents($file));
			}
		}
		return $zip->close();
	}
}

