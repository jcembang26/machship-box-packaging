<?php

namespace Tests\Feature;

use Tests\TestCase;

class BasicPackingScenarioTest extends TestCase
{

    protected $apiUrl = '/api/pack-products';

    protected $cols = [
        'name',
        'volume',
        'max_weight',
        'items'
    ];

    protected $postData = [
        "products" => [
            [
                "id" => "1",
                "name" => "Product 1",
                "length" => "1",
                "width" => "1",
                "height" => "1",
                "weight" => "1",
                "quantity" => "1",
            ],
            [
                "id" => "2",
                "name" => "Product 2",
                "length" => "2",
                "width" => "2",
                "height" => "2",
                "weight" => "2",
                "quantity" => "1",
            ],
            [
                "id" => "3",
                "name" => "Product 3",
                "length" => "1",
                "width" => "2",
                "height" => "2",
                "weight" => "2",
                "quantity" => "5",
            ],
            [
                "id" => "4",
                "name" => "Product 4",
                "length" => "1",
                "width" => "2",
                "height" => "2",
                "weight" => "1",
                "quantity" => "4",
            ],
            [
                "id" => "5",
                "name" => "Product 5",
                "length" => "2",
                "width" => "2",
                "height" => "2",
                "weight" => "1",
                "quantity" => "1",
            ]
        ]
    ];

    protected $postDataInOneBox = [
        "products" => [
            [
                "id" => "1",
                "name" => "Product 1",
                "length" => "1",
                "width" => "1",
                "height" => "1",
                "weight" => "1",
                "quantity" => "1",
            ],
            [
                "id" => "2",
                "name" => "Product 2",
                "length" => "2",
                "width" => "2",
                "height" => "2",
                "weight" => "1",
                "quantity" => "1",
            ],
            [
                "id" => "3",
                "name" => "Product 3",
                "length" => "1",
                "width" => "2",
                "height" => "2",
                "weight" => "1",
                "quantity" => "1",
            ],
            [
                "id" => "4",
                "name" => "Product 4",
                "length" => "1",
                "width" => "2",
                "height" => "2",
                "weight" => "1",
                "quantity" => "1",
            ],
            [
                "id" => "5",
                "name" => "Product 5",
                "length" => "2",
                "width" => "2",
                "height" => "2",
                "weight" => "1",
                "quantity" => "1",
            ]
        ]
    ];

    protected $postDataInBox = [
        "products" => [
            [
                "id" => "1",
                "name" => "Product 1",
                "length" => "30",
                "width" => "10",
                "height" => "10",
                "weight" => "5",
                "quantity" => "1",
            ],
            [
                "id" => "2",
                "name" => "Product 2",
                "length" => "50",
                "width" => "30",
                "height" => "10",
                "weight" => "10",
                "quantity" => "1",
            ]
        ]
    ];

    protected $largeProduct = [
        "products" => [
            [
                "id" => "1",
                "name" => "Product 1",
                "length" => "300",
                "width" => "100",
                "height" => "100",
                "weight" => "5",
                "quantity" => "1",
            ]
        ]
    ];

    protected $higherTotalVolumeProducts = [
        "products" => [
            [
                "id" => "1",
                "name" => "Product 1",
                "length" => "150",
                "width" => "20",
                "height" => "55",
                "weight" => "5",
                "quantity" => "1",
            ],
            [
                "id" => "2",
                "name" => "Product 2",
                "length" => "10",
                "width" => "10",
                "height" => "15",
                "weight" => "1",
                "quantity" => "1",
            ],
            [
                "id" => "3",
                "name" => "Product 3",
                "length" => "10",
                "width" => "10",
                "height" => "15",
                "weight" => "1",
                "quantity" => "1",
            ],
        ]
    ];

    protected $largeProducts = [
        "products" => [
            [
                "id" => "1",
                "name" => "Product 1",
                "length" => "150",
                "width" => "20",
                "height" => "55",
                "weight" => "5",
                "quantity" => "1",
            ],
            [
                "id" => "2",
                "name" => "Product 2",
                "length" => "150",
                "width" => "20",
                "height" => "55",
                "weight" => "5",
                "quantity" => "1",
            ],
            [
                "id" => "3",
                "name" => "Product 3",
                "length" => "150",
                "width" => "20",
                "height" => "55",
                "weight" => "5",
                "quantity" => "1",
            ],
        ]
    ];


    protected $productQuantities = [
        "products" => [
            [
                "id" => "2",
                "name" => "Product 1",
                "length" => "1",
                "width" => "1",
                "height" => "1",
                "weight" => "1",
                "quantity" => "6",
            ],
            [
                "id" => "3",
                "name" => "Product 2",
                "length" => "1",
                "width" => "1",
                "height" => "1",
                "weight" => "1",
                "quantity" => "4",
            ],
        ]
    ];
    

    /**
     * Check JSON Structure response
     */
    public function test_response()
    {
        // Perform the POST request
        $response = $this->post($this->apiUrl, $this->postData);

        // Assert that the request was successful (status code 200)
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                '*' => $this->cols
            ]
        );
    }

    /**
     * Check response keys
     *
     */
    public function test_keys()
    {
        $response = $this->post($this->apiUrl, $this->postData);

        $result = json_decode($response->getContent());

        if (empty($result)) {
            $this->assertEquals(0, 0);
        } else {
            $keys = array_keys(get_object_vars($result[0]));
            $this->assertEquals(count($keys), count($this->cols));

            foreach ($keys as $row) {
                $this->assertContains($row, $this->cols);
            }

            $items = $result[0]->items;

            $this->assertNotNull($items);
        }
    }

    /**
     * fit all products in one box
     */
    public function test_fit_all_products_in_one_box()
    {
        $response = $this->post($this->apiUrl, $this->postDataInOneBox);
        $result = json_decode($response->getContent());

        $countBox = count($result);
        $countItem = count($result[0]->items);

        // only 1 box
        $this->assertEquals($countBox, 1);
        
        // all items should be inside the box
        $this->assertEquals($countItem, 5);
    }

    /**
     * Each product should be assigned with a box
     */
    public function test_fit_each_products_in_a_box()
    {
        $response = $this->post($this->apiUrl, $this->postDataInBox);
        $result = json_decode($response->getContent());

        $countBox = count($result);

        $this->assertEquals($countBox, count($this->postDataInBox['products']));
        
        foreach($result as $res){
            // each box should only get 1 items only
            $this->assertEquals(count($res->items), 1);
        }
    }

    /**
     * Product is to large, no box found
     *
     * @return void
     */
    public function test_product_too_large()
    {
        $response = $this->post($this->apiUrl, $this->largeProduct);
        $response->assertStatus(500);
    }

    /**
     * Each product match the largest box
     *
     */
    public function test_exact_match_largest_match()
    {
        $response = $this->post($this->apiUrl, $this->largeProducts);
        $result = json_decode($response->getContent());

        $countBox = count($result);

        // all box should be used
        $this->assertEquals($countBox, count($this->largeProducts['products']));

        foreach ($result as $res) {

            // each box should only get 1 items only
            $this->assertEquals(count($res->items), 1);
        }
    }
    
    /**
     * empty product list
     *
     */
    public function test_empty_products()
    {
        // Perform the POST request
        $response = $this->post($this->apiUrl, [
            "products" => []
        ]);
        
        $response->assertStatus(302);
    }
    
    /**
     * Product total volumes exceeds box volume
     *
     * @return void
     */
    public function test_total_volume_exceeds()
    {
        $response = $this->post($this->apiUrl, $this->higherTotalVolumeProducts);
        $result = json_decode($response->getContent());

        $countBox = count($result);

        // total of 2 box
        // first box contains the largest
        // second box contains the remaining products
        $this->assertEquals($countBox, 2);

        // first box should only contain the largest item
        // therefore the count should be 1
        $this->assertEquals(count($result[0]->items), 1);

        // the remaining box should contain all remaining
        // products excluding the first product (largest)
        $remainingProductCount = count($this->higherTotalVolumeProducts['products']) - 1;
        
        $this->assertEquals(count($result[1]->items), $remainingProductCount);
    }

    /**
     * Product quantities carry over
     *
     */
    public function test_product_quantity()
    {
        $response = $this->post($this->apiUrl, $this->productQuantities);
        $result = json_decode($response->getContent());

        $countBox = count($result);

        // array should contain 2 box
        $this->assertEquals($countBox, 2);

        // first box should contain 5 items due to it's weight limit
        $this->assertEquals(count($result[0]->items), 5);

        // second box should also contain 5 items due to the carry over from the first box
        $this->assertEquals(count($result[1]->items), 5);

    }
}
