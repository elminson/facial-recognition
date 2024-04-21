<?php
require 'vendor/autoload.php';

use Aws\Rekognition\RekognitionClient;
use Aws\Credentials\Credentials;
use Dotenv\Dotenv;

class RekognitionService
{
	private $rekognitionClient;

	public function __construct()
	{
		$dotenv = Dotenv::createImmutable(__DIR__);
		$dotenv->load();

		$credentials = new Credentials($_ENV['AWS_ACCESS_KEY'], $_ENV['AWS_SECRET_KEY']);

		$this->rekognitionClient = RekognitionClient::factory([
																  'region' => "us-east-1",
																  'version' => 'latest',
																  'credentials' => $credentials
															  ]);
	}

	public function compareFacesInDirectory($directory, $searchImage)
	{
		$files = glob($directory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
		$totalNoMatch = 0;
		$totalMatch = 0;
		$results = [];

		foreach($files as $file) {
			$compareFaceResults = $this->rekognitionClient->compareFaces([
																			 'SimilarityThreshold' => 90,
																			 'SourceImage' => [
																				 'Bytes' => file_get_contents($searchImage)
																			 ],
																			 'TargetImage' => [
																				 'Bytes' => file_get_contents($file)
																			 ],
																		 ]);

			$FaceMatchesResult = $compareFaceResults['FaceMatches'];
			$SimilarityResult =  @$FaceMatchesResult[0]['Similarity'] ?? 'NO_MATCH';
			$sourceImageFace = $compareFaceResults['SourceImageFace'];
			$sourceConfidence = $sourceImageFace['Confidence'];

			if ($SimilarityResult == 'NO_MATCH') {
				$totalNoMatch++;
			} else{
				$totalMatch++;
			}

			$results[] = [
				'file' => $file,
				'searchImage' => $searchImage,
				'SimilarityResult' => $SimilarityResult,
				'sourceConfidence' => $sourceConfidence
			];
		}

		$summary = [
			"Total Image Processed" => count($files),
			"Total Image Matched" => $totalMatch,
			"Total Image Not Matched" => $totalNoMatch
		];

		return ['results' => $results, 'summary' => $summary];
	}
}

$rekognitionService = new RekognitionService();
$result = $rekognitionService->compareFacesInDirectory('brayan_images/same_person', 'brayan_images/same_person/search_this_person.png');
print_r($result);
