<?php

function transformToCoordinates($locations)
{
	$coordinates = [];
	foreach ($locations as $key => $location) {
		if (count($coordinates) > 0) {
			if ($location['lon'] - $coordinates[count($coordinates) - 1]['lon'] > 180) {
				foreach ($coordinates as $key => $value) {
				 	$coordinates[$key]['lon'] = $coordinates[$key]['lon'] + 360;
				 } 
			} elseif ($coordinates[count($coordinates) - 1]['lon'] - $location['lon']> 180) {
				foreach ($coordinates as $key => $value) {
					$coordinates[$key]['lon'] = $coordinates[$key]['lon'] - 360;
				}
			}
		}
		$coordinates[] = $location;
	}

	return $coordinates;
}

function calculateLocations($locations) 
{
	$lonMin = NULL;
	$lonMax = NULL;
	$latMin = NULL;
	$latMax = NULL;
	foreach ($locations as $location) {
		$lonMin = (is_null($lonMin)) ? $location['lon'] : $lonMin;
		$lonMin = ($lonMin > $location['lon']) ? $location['lon'] : $lonMin;

		$lonMax = (is_null($lonMax)) ? $location['lon'] : $lonMax;
		$lonMax = ($lonMax < $location['lon']) ? $location['lon'] : $lonMax;

		$latMin = (is_null($latMin)) ? $location['lat'] : $latMin;
		$latMin = ($latMin > $location['lat']) ? $location['lat'] : $latMin;

		$latMax = (is_null($latMax)) ? $location['lat'] : $latMax;
		$latMax = ($latMax < $location['lat']) ? $location['lat'] : $latMax;
	}
	return [
		'lonMin' => $lonMin,
		'lonMax' => $lonMax,
		'latMin' => $latMin,
		'latMax' => $latMax,
	];
}

function transformByOrigin(&$locations, $origin)
{
	foreach ($locations as $key => $location) {
		$locations[$key]['lon'] = $location['lon'] - $origin['lon'];
		$locations[$key]['lat'] = $origin['lat'] - $location['lat'];
	}
}

function drawRoute(&$locations, $lonWidth, $latLength, $size = 500)
{
	$lonSize = 0;
	$latSize = 0;
	if ($lonWidth >= $latLength) {
		$lonSize = $size;
		$latSize = $size * ($latLength / $lonWidth);
	} else {
		$latSize = $size;
		$lonSize = $size * ($lonWidth / $latLength);
	}

	foreach ($locations as $key => $location) {
		$locations[$key]['lon'] = $lonSize * ($location['lon'] / $lonWidth);
		$locations[$key]['lat'] = $latSize * ($location['lat'] / $latLength);
	}

	$im = imagecreate($lonSize, $latSize);
	$white = imagecolorallocate($im, 255, 255, 255);
	$red = imagecolorallocate($im, 255, 0, 0);
	imagefill($im, 0, 0, $white);
	for ($i = 0; $i < count($locations); $i++) {
		if ($i != 0) {
			imageline($im, $locations[$i-1]['lon'], $locations[$i-1]['lat'], $locations[$i]['lon'], $locations[$i]['lat'], $red);
		}
	}
	imagejpeg($im, '/home/tony/Desktop/cardCover.jpg');
	imagedestroy($im);

	printf("lonWidth:" . $lonWidth . "\n");
	printf("latLength:" . $latLength . "\n");
	printf("lonSize:" . $lonSize . "\n");
	printf("latSize:" . $latSize . "\n");
}

/*$locations = [
	['name' => 'London', 'lon' => 0, 'lat' => 52],
	['name' => 'Paris', 'lon' => 2, 'lat' => 49],
	['name' => 'Rome', 'lon' => 12, 'lat' => 42],
	['name' => 'Vienna', 'lon' => 16, 'lat' => 48],
	['name' => 'Berlin', 'lon' => 13, 'lat' => 52],
	['name' => 'Madrid', 'lon' => -4, 'lat' => 40],
	['name' => 'Oslo', 'lon' => 11, 'lat' => 60],
];*/

$locations = [
	['name' => 'Beijing', 'lon' => 116, 'lat' => 40],
	['name' => 'Wellington', 'lon' => 174, 'lat' => -41],
	['name' => 'San Francisco', 'lon' => -122, 'lat' => 38],
	['name' => 'Mexico City', 'lon' => -99, 'lat' => 20],
	//['name' => 'London', 'lon' => 0, 'lat' => 51],
	//['name' => 'Singapo', 'lon' => 104, 'lat' => 1],
	//['name' => 'Sydny', 'lon' => 151, 'lat' => -34],
	//['name' => 'Toronto', 'lon' => -79, 'lat' => 44],
	['name' => 'Tokyo' , 'lon' => 140, 'lat' => 36],
];

$coordinates = transformToCoordinates($locations);
$statistic = calculateLocations($coordinates);
$lonMin = $statistic['lonMin'];
$lonMax = $statistic['lonMax'];
$latMin = $statistic['latMin'];
$latMax = $statistic['latMax'];
$origin = ['lon' => $lonMin, 'lat' => $latMax];
transformByOrigin($coordinates, $origin);
$lonWidth = $lonMax - $lonMin;
$latLength = $latMax - $latMin;
drawRoute($coordinates, $lonWidth, $latLength);
