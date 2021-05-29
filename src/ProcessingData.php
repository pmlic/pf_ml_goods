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
    protected $predict_data = [0,6.1];
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
            $tmp = [];
            $dataset = $this->get_data();
            $train_data = $dataset->getSamples();
            $train_label = $dataset->getTargets();
            foreach (range(1, 10) as $k) {
                $correct = 0;
                foreach ($dataset->getSamples() as $index => $sample) {
                    $estimator = new \Phpml\Classification\KNearestNeighbors($k);
                    $estimator->train($this->removeIndex($index, $dataset->getSamples()), $this->removeIndex($index, $dataset->getTargets()));
                    $predicted = $estimator->predict([$sample]);
                    var_dump($predicted);
                    if ($predicted[0] == $train_label[$index]) {
                        $correct++;
                    }
                }
                echo sprintf('Accuracy (k=%s): %.02f%% correct: %s', $k, ($correct / count($train_data)) * 100, $correct) . PHP_EOL;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function removeIndex($index, $array) {
        unset($array[$index]);
        return $array;
    }
}

$pro = new ProcessingData('purchase.csv');
$pro->get_similar_user();
