<?php

/**
 * Created by PhpStorm.
 * User: PFinal南丞
 * Email: Lampxiezi@163.com
 * Date: 2020/5/25
 * Time: 11:19
 */
require __DIR__ . '/../vendor/autoload.php';

use Phpml\Preprocessing\Imputer;

class ProcessingData
{
    protected $data_path;

    public function __construct($data_filename)
    {
        $this->data_path = __DIR__ . '/../data/' . $data_filename;
    }

    public function get_data()
    {
        //读取csv文件
        $dataset = new \Phpml\Dataset\CsvDataset($this->data_path, 5, true);
        $randomSplit = new \Phpml\CrossValidation\RandomSplit($dataset, 0.3);
        return $randomSplit;
    }

    public function get_similar_user()
    {
        try {
            $dataset = $this->get_data();
            $train_data = $dataset->getTrainSamples();
            $train_data = @\pf\arr\PFarr::pf_arr_remove_empty($train_data, '', true);
            $train_label = $dataset->getTrainLabels();
            $train_label = @\pf\arr\PFarr::pf_arr_remove_empty($train_label, '', true);
            $classifier = new \Phpml\Classification\KNearestNeighbors($k = 3, new \Phpml\Math\Distance\Minkowski());
            $classifier->train($train_data, $train_label);
            $test_data = $dataset->getTestSamples();
            $test_data = @\pf\arr\PFarr::pf_arr_remove_empty($test_data, '', true);
            if (count($test_data) > 0) {
                $result = $classifier->predict($test_data);
                print_r($result);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}

$pro = new ProcessingData('purchase.csv');
$pro->get_similar_user();
