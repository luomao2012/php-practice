<?php

/**
 * 计算宝箱的概率
 * 1. 宝箱中各道具可设计概率，最小小数点后两位。
 * 2. 多个宝箱开出重复道具，道具数量可以叠加。
 */

class Box
{
    public $reward = [];
    public $cycleCount = 10000;

    public function __construct()
    {
        $this->reward = [
            [
                'id'     => 1,
                'number' => 1,
                'rate'   => 2000,
            ],
            [
                'id'     => 2,
                'number' => 1,
                'rate'   => 2000,
            ],
            [
                'id'     => 3,
                'number' => 1,
                'rate'   => 2000,
            ],
            [
                'id'     => 4,
                'number' => 1,
                'rate'   => 2000,
            ],
            [
                'id'     => 5,
                'number' => 1,
                'rate'   => 2000,
            ],
        ];
        $this->reward = json_decode(json_encode($this->reward));
    }

    /**
     * 设置宝箱
     */
    public function setBox($reward)
    {

    }

    /**
     * 打开单个宝箱
     */
    public function openSingle()
    {
        $rewardTools = [];
        $random = mt_rand(0, $this->cycleCount);
        foreach ($this->reward as $v) {
            if ($random <= $v->rate) {
                $tmp = [
                    'id'     => $v->id,
                    'number' => $v->number,
                ];
                $rewardTools = $tmp;
                break;
            } else {
                $random = $random - $v->rate;
            }
        }
        return $rewardTools;
    }

    /**
     * 打开多个宝箱
     */
    public function openMulti($number)
    {
        $rewardTools = [];
        for ($i = 0; $i < $number; $i++) {
            $random = mt_rand(0, $this->cycleCount);
            foreach ($this->reward as $v) {
                if ($random <= $v->rate) {
                    //合并相同道具
                    if (isset($rewardTools[$v->id])) {
                        $rewardTools[$v->id]['number'] += $v->number;
                    } else {
                        $tmp = [
                            'id'     => $v->id,
                            'number' => $v->number,
                        ];
                        $rewardTools[$v->id] = $tmp;
                    }
                    break;
                } else {
                    $random = $random - $v->rate;
                }
            }
        }
        return $rewardTools;
    }

    /**
     * 循环10000次计算真实宝箱概率
     *
     * @Test
     */
    public function testRealBoxRate()
    {
        $tools = [];
        $base = 10000;
        for ($i = 0; $i < $base; $i++) {
            $rewardTool = $this->openSingle();
            if (isset($tools[$rewardTool['id']])) {
                $tools[$rewardTool['id']]['number'] += $rewardTool['number'];
            } else {
                $tools[$rewardTool['id']] = $rewardTool;
            }
        }
        return $tools;
    }

    public function printBoxRate($tools, $size)
    {
        $tools = array_values($tools);
        foreach ($tools as $index => $tool) {
            print_r("[$index] tool_id={$tool['id']}, rate=" . round($tool['number'] / $size, 2) . "\n");
        }
    }

}

$box = new Box();
$tools = $box->testRealBoxRate();
$box->printBoxRate($tools, $box->cycleCount);
$tools = $box->openMulti(1000);
$box->printBoxRate($tools, 1000);