<?php

namespace loisium;

class Spider {
    private $config = null;

    public function __construct() {
        $this->config = new Config();

        $this->config->set('hello.world.what', 124);
        $this->config->set('hello.world.cat', 125);
        $this->config->set('hello.cat', 53);
        $this->config->load(['hello'=>['world'=>['abc'=>'ccc'], 'what' => 555, 'aaa' => ['bbb' => 5557]]], false);
        $this->config->load(['hello'=>['world'=>['abc'=>'ccc'], 'what' => 555, 'aaa1' => ['bbb' => 5587]]], true);
        print_r($this->config->get(''));
        print_r($this->config->get('hello'));
        print_r($this->config->get('hello.world'));
    }

    public function checkRequirement() {
        if (!Environment::checkRequirement()) {
            echo Environment::getInfo();
            exit;
        }
    }
}