<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackProductRequest;
use Illuminate\Http\Request;

class PackageController extends Controller
{

    public function __construct() {
        $this->availableBox = [
            ["name" => "BOXA", "length" => 20, "width" => 15, "height" => 10, "weight_limit" => 5],
            ["name" => "BOXB", "length" => 30, "width" => 25, "height" => 20, "weight_limit" => 10],
            ["name" => "BOXC", "length" => 60, "width" => 55, "height" => 50, "weight_limit" => 50],
            ["name" => "BOXD", "length" => 50, "width" => 45, "height" => 40, "weight_limit" => 30],
            ["name" => "BOXE", "length" => 40, "width" => 35, "height" => 30, "weight_limit" => 20],
        ];
    }
    
    /**
     * handles box selection and segregation of products
     *
     * @param PackProductRequest $request
     * @return object
     */
    public function pack(PackProductRequest $request): object
    {

        if(!$request->products || empty($request->products)){
            return response()->json(['message' => 'Missing required parameter/s!'], 400);
        }

        $availableBoxes = $this->processBoxDetails($this->availableBox);
        $details = $this->processProductDetails($request->products);

        // start packing
        // Initialize boxes array
        $boxes = [];

        // Initialize the first box to be used
        $firstBox = null;

        $canPutTogether = $this->isPossibleToPackTogether($details['total_volume'], $availableBoxes);
        
        foreach ($details['products'] as $key => $value) {

            // Find a box that will fit each product
            // Start by attempting to fit the product into the smallest available box
            if (!$firstBox) {
                foreach ($availableBoxes as $box) {
                    if ($box['volume'] >= $value['volume']) {
                        // Product fits in this box, so use this as the initial box
                        $firstBox = $box;
                        break;
                    }
                }
            }

            if ($firstBox) {
                // Check if the product can be added to an existing box or if a new box is needed
                $boxAdded = false;
                foreach ($boxes as &$box) {

                    // Extract values of the specified key into a new array
                    $values = array_column($box['items'], 'weight');

                    // Calculate the sum of the values
                    $currentWeight = array_sum($values);

                    $remaining = $box['max_weight'] - $currentWeight;

                    // Extract values of the specified key into a new array
                    $itemVolumes = array_column($box['items'], 'volume');

                    // Calculate the sum of the values
                    $currentVolume = array_sum($itemVolumes);

                    $remainingVolume = $box['volume'] - $currentVolume;

                    if ($remainingVolume >= $value['volume'] && $remaining >= $value['weight']) {
                        // Product fits in this box, add it to the items array of the box
                        $box['items'][] = [
                            "name" => $value['name'],
                            "volume" => $value['volume'],
                            "weight" => floatval($value['weight'])
                        ];
                        $boxAdded = true;
                        break;
                    }
                }

                if (!$boxAdded) {
                    // Create a new box and add it to the $boxes array
                    $newBox = [
                        "name" => $firstBox['name'],  // Replace with actual box name
                        'volume' => $firstBox['volume'],
                        "max_weight" => $firstBox['weight_limit'], // Replace with actual max weight
                        "items" => [
                            [
                                "name" => $value['name'],
                                "volume" => $value['volume'],
                                "weight" => floatval($value['weight'])
                            ]
                        ]
                    ];
                    $boxes[] = $newBox;
                }

                if ($key === 0 && !$canPutTogether) {
                    $firstBox = null;
                }

            } else {
                return response()->json(['message' => 'No matching box to use!'], 500);
            }
        }

        return response()->json($boxes);
    }
    
    /**
     * Map existing box details to output only needed details as well as sort from the smallest box
     *
     * @param array $boxes
     * @return array
     */
    private function processBoxDetails(array $boxes = []): array
    {
        if(!$boxes || empty($boxes)){
            return [];
        }

        $newBoxes = [];

        foreach ($boxes as $value) {
            $newBoxes[] = [
                'name' => $value['name'],
                'volume' => $this->computeVolume($value['length'], $value['width'], $value['height']),
                'weight_limit' => $value['weight_limit']
            ];
        }

        usort($newBoxes, function ($a, $b) {
            return $a['volume'] - $b['volume'];
        });

        return $newBoxes;

    }

    /**
     * map existing product details to output only needed details as well as sort from the largest product
     *
     * @param array $products
     * @return array
     */
    private function processProductDetails(array $products = []): array
    {
        if (!$products || empty($products)) {
            return [];
        }

        $newProducts = [];

        foreach ($products as $value) {

            for ($i=0; $i < $value['quantity']; $i++) {
                $newProducts['products'][] = [
                    "name" => $value['name'],
                    'volume' => $this->computeVolume($value['length'], $value['width'], $value['height']),
                    'quantity' => 1,
                    'weight' => $value['weight'],
                    'total_weight' => $value['quantity'] * $value['weight']
                ];
            }
        }

        usort($newProducts['products'], function ($a, $b) {
            return $b['volume'] - $a['volume'];
        });
        
        $newProducts['total_volume'] = $this->sumAll($newProducts['products'], 'volume');
        $newProducts['total_weight'] = $this->sumAll($newProducts['products'], 'weight');

        return $newProducts;
    }
    
    /**
     * Computes object volume
     *
     * @param float $length
     * @param float $width
     * @param float $height
     * @return float
     */
    private function computeVolume(float $length = 0.0, float $width = 0.0, float $height = 0.0): float
    {
        return $length * $width * $height;
    }

    /**
     * addition of all the values depending on the given key name
     *
     * @param array $arr
     * @param string $key
     * @return float
     */
    private function sumAll(array $arr = [], string $key = 'volume'): float
    {
        $totalVolume = 0;

        if(empty($arr)){
            return $totalVolume;
        }

        foreach ($arr as $value) {
            if($value[$key]){
                $totalVolume += $value[$key];
            }
        }

        return $totalVolume;
    }

    /**
     * determines if products can fit into one box
     *
     * @param float $totalProductVolume
     * @param array $availableBoxes
     * @return boolean
     */
    private function isPossibleToPackTogether(float $totalProductVolume = 0.0, array $availableBoxes = []): bool
    {

        if($totalProductVolume === 0.0){
            return true;
        }

        if(empty($availableBoxes)){
            return false;
        }

        $findBox = array_filter($availableBoxes, function ($product) use ($totalProductVolume) {
            return $product['volume'] > $totalProductVolume;
        });

        return !empty($findBox);
    }
}
