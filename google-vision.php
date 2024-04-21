<?php
require 'vendor/autoload.php';

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Dotenv\Dotenv;

class GoogleVisionService
{
	private $imageAnnotator;

	public function __construct()
	{
		$dotenv = Dotenv::createImmutable(__DIR__);
		$dotenv->load();

		putenv('GOOGLE_APPLICATION_CREDENTIALS='.$_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
		$this->imageAnnotator = new ImageAnnotatorClient();
	}

	public function detectFaces($path)
	{
		$image = file_get_contents($path);
		$response = $this->imageAnnotator->faceDetection($image);
		file_put_contents($path."_data.json",print_r(json_decode($response->serializeToJsonString()), true));
		$faces = $response->getFaceAnnotations();

		return $faces;
	}

	public function compareFaces($path1, $path2)
	{
		$faces1 = $this->detectFaces($path1);
		$faces2 = $this->detectFaces($path2);

		if (count($faces1) != count($faces2)) {
			return false; // If the number of faces detected is different, they are not the same person
		}

		// Compare facial features or bounding box coordinates to determine similarity
		// For simplicity, you might compare face bounding box coordinates
		// Or you can use facial landmarks for more accurate comparison

		// Your comparison logic here

		return true; // Return true if the images likely contain the same person
	}

	public function compareFacesInDirectory($directory, $searchImage)
	{
		$files = glob($directory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
		$results = [];

		foreach ($files as $file) {
			$result = $this->compareFaces($searchImage, $file);
			$results[] = [
				'file' => $file,
				'searchImage' => $searchImage,
				'comparisonResult' => $result
			];
		}

		return $results;
	}

	public function __destruct()
	{
		$this->imageAnnotator->close();
	}
}

$googleVisionService = new GoogleVisionService();
$results = $googleVisionService->compareFacesInDirectory('brayan_images/same_person', 'brayan_images/same_person/search_this_person.png');

foreach ($results as $result) {
	if ($result['comparisonResult']) {
		echo "The images {$result['file']} and {$result['searchImage']} likely contain the same person.\n";
	} else {
		echo "The images {$result['file']} and {$result['searchImage']} likely do not contain the same person.\n";
	}
}
