# Facial Recognition Project

This project uses the Google Cloud Vision API and AWS Rekognition API to perform facial recognition tasks.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP
- Composer

### Installing

1. Clone the repository
```BASH
git clone git@github.com:elminson/facial-recognition.git
```
2. Install dependencies
```BASH
composer install
```
3. Copy the `.env.example` file to `.env` and fill in your AWS and Google Cloud credentials.

### Usage

## Google Cloud Vision

This project contains two main classes: `GoogleVisionService` and `RekognitionService`.

- `GoogleVisionService` uses the Google Cloud Vision API to detect faces in images and compare faces in different images.
- `RekognitionService` uses the AWS Rekognition API to compare faces in different images.

Here's an example of how to use these classes:

```php
$googleVisionService = new GoogleVisionService();
$results = $googleVisionService->compareFacesInDirectory('brayan_images/same_person', 'brayan_images/same_person/search_this_person.png');

foreach ($results as $result) {
    if ($result['comparisonResult']) {
        echo "The images {$result['file']} and {$result['searchImage']} likely contain the same person.\n";
    } else {
        echo "The images {$result['file']} and {$result['searchImage']} likely do not contain the same person.\n";
    }
}
```
## AWS Rekognition

This project also uses the AWS Rekognition API to perform facial recognition tasks. The `RekognitionService` class encapsulates the functionality of the AWS Rekognition API.

Here's an example of how to use this class:

```php
$rekognitionService = new RekognitionService();
$result = $rekognitionService->compareFacesInDirectory('brayan_images/same_person', 'brayan_images/same_person/search_this_person.png');
print_r($result);


