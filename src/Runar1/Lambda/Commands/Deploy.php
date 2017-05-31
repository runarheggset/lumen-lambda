<?php 

namespace Runar1\Lambda\Commands;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

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
			'credentials' => [
				'key'		=> env('LAMBDA_KEY'),
				'secret'	=> env('LAMBDA_SECRET'),
			],
			'region'	=> env('LAMBDA_REGION'),
			'version'	=> '2015-03-31'
		]);
		$response = $client->updateFunctionCode([
			'FunctionName' 	=> $this->argument('name'),
			'ZipFile'		=> $data,
			'Publish'		=> true,
		]);
		$client->createAlias([
    		'FunctionName' 		=> $this->argument('name'),
    		'Name' 				=> 'v' . $response->get('Version'),
			'FunctionVersion' 	=> $response->get('Version'),
		]);
	}

	private function createZipFile() {
		if (!extension_loaded('zip')) {
			throw new \Exception('ZIP Extension not loaded.');
		}

		$rootPath = realPath('.');
		$zip = new ZipArchive();

		if (!$zip->open(storage_path('app/app.zip'), ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
			return false;
		}

		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($rootPath), 
			RecursiveIteratorIterator::LEAVES_ONLY
		);
		foreach ($files as $name => $file) {
			if ($file->isDir()) {
				continue;
			}
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($rootPath) + 1);
			$relativePath = str_replace('\\', '/', $relativePath);

			$parts = explode('/', $relativePath);

			if (in_array($parts[count($parts) - 1], ['app.zip', '.', '..', '.env'])) {
				continue;
			}
			if (in_array('.git', $parts)) {
				continue;
			}
			$zip->addFile($filePath, $relativePath);
			if (in_array('php-cgi', $parts)) {
				$zip->setExternalAttributesName($relativePath, ZipArchive::OPSYS_UNIX, 2180972544);
			}
		}
		return $zip->close();
	}
}
