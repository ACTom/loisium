<?php

namespace loisium;

use loisium\Cache\RedisCache;
use loisium\Input\HttpInput;
use loisium\Output\DbOutput;
use loisium\Rule\ArticleRule;

class Spider {
    private $config = null;
    private $engine = null;

    public function __construct() {
        $this->config = new Config();
        $this->engine = new Engine();
        $this->engine->setInput(new HttpInput());
        $this->engine->setOutput(new DbOutput());
        $this->engine->setCache(new RedisCache(new Config($this->config->get('cache'))));
        $this->engine->addRule(new ArticleRule(), 1);
    }

    public function run() {
        $this->engine->start();
    }

    public function checkRequirement() {
        if (!Environment::checkRequirement()) {
            echo Environment::getInfo();
            exit;
        }
    }
}