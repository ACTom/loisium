<?php

namespace loisium\Tests;

require "../vendor/autoload.php";

use loisium\Cache\RedisCache;
use loisium\Config;
use loisium\Engine;
use loisium\Input\IInput;
use loisium\Output\IOutput;
use loisium\Rule\IRule;

class NumberInput implements IInput {

    private $engine;

    public function __construct(Engine $engine) {
        $this->engine = $engine;
    }

    public function get($data): string {
        $result = mt_rand(0, 1000);
        if ($result !== mt_rand(0, 100)) {
            $this->engine->getCache()->push($this->engine->getTaskId(), $result);
        }
        usleep(mt_rand(1000, 100000));
        return $result;
    }

}

class NumberRule implements IRule {
    private $ruleId = 100;

    public function getRuleId(): int {
        return $this->ruleId;
    }

    public function process(string &$source, &$data) {
        if (!is_array($data)) {
            $data = [];
        }

        array_push($data, ['number' => (int)$source]);
    }
}

class ScreenOutput implements IOutput {
    public function write($data) {
        foreach ($data as $item) {
            $output = date('[Y-m-d H:i:s]') . " output:{$item['number']}\n";
            echo $output;
        }
    }

}

class EngineTest {
    private $engine = null;

    public function __construct() {
        $this->engine = new Engine();
        $this->engine->setInput(new NumberInput($this->engine));
        $this->engine->setOutput(new ScreenOutput());
        $this->engine->setCache(new RedisCache(new Config()));
        $this->engine->addRule(new NumberRule(), 1);
        $this->engine->setTaskNum(10);
        $this->engine->getCache()->push($this->engine->getTaskId(), '0');
    }

    public function run() {
        $this->engine->start();
    }
}

$spider = new EngineTest();
$spider->run();