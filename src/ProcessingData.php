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
        return new \Phpml\Dataset\CsvDataset($this->data_path, 1, true, "|");
    }

    public function get_similar_user()
    {
        try {
            $dataset = $this->get_data();
            $train_data = $dataset->getSamples();
            $train_data = @\pf\arr\PFarr::pf_arr_remove_empty($train_data, '', true);
            $train_label = $dataset->getTargets();
            $train_label = @\pf\arr\PFarr::pf_arr_remove_empty($train_label, '', true);
            $train_list = $this->get_merge_data($train_label, $train_data);
            // 计算商品的相似度
            $similarity = $this->get_similarity($train_list);

//            $classifier = new \Phpml\Classification\KNearestNeighbors($k = 3, new \Phpml\Math\Distance\Minkowski());
//            $classifier->train($train_data, $train_label);
//            $test_data = $dataset->getTestSamples();
//            $test_data = @\pf\arr\PFarr::pf_arr_remove_empty($test_data, '', true);
//            if (count($test_data) > 0) {
//                $result = $classifier->predictProbability($test_data);
//                print_r($result);
//            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function get_merge_data($train_label, $train_data): array
    {
        $tmp_data = [];
        $tmp = [];
        foreach ($train_data as $k => $value) {
            $tmp_data[$train_label[$k]] = $value[0];
        }
        foreach ($tmp_data as $k1 => $value1) {
            foreach ($tmp_data as $k2 => $value2) {
                if ($k2 != $k1 && !isset($tmp[$k1 . $k2]) && !isset($tmp[$k2 . $k1])) {
                    $tmp_val_a = explode(',', $value1);
                    $tmp_val_b = explode(',', $value2);
                    $tmp[$k1 . $k2] = [$tmp_val_a, $tmp_val_b];
                }
            }
        }
        return $tmp;
    }
}

$pro = new ProcessingData('purchase.csv');
$pro->get_similar_user();
