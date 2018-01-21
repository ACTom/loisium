<?php

namespace loisium;

use loisium\Cache\RedisCache;
use loisium\Input\HttpInput;
use loisium\Output\DbOutput;
use loisium\Rule\ArticleRule;

class SampleSpider {
    private $config = null;
    private $engine = null;

    /**
     * SampleSpider constructor.
     * @throws \RedisException
     */
    public function __construct() {
        global $argv;
        $dbName = dirname($argv[0]) . '/data.db';
        if (!file_exists($dbName)) {
            $this->createDbFile($dbName);
        }
        $this->config = new Config([
            'db' => [
                'dsn' => "sqlite:{$dbName}",
                'table' => 'thread'
            ],
        ]);
        $this->engine = new Engine();
        $this->engine->setInput(new HttpInput());
        $this->engine->setOutput(new DbOutput($this->config->get('db')));
        $this->engine->setCache(new RedisCache(new Config($this->config->get('cache'))));
        $rule = new ArticleRule($this->engine);
        $rule->setListRegex('/https:\/\/www.libreofficechina.org\/forum-\d+-\d+\.html/');
        $rule->setArticleRegex('/https:\/\/www.libreofficechina.org\/thread-\d+-\d+-\d+.html/');
        $rule->setItemRules([
            [
                'name' => 'title',
                'type' => 'xpath',
                'selector' => '//*[@id="thread_subject"]'
            ],
            [
                'name' => 'author',
                'type' => 'xpath',
                'selector' => '//*[@id="pid6919"]/tbody/tr[1]/td/div[1]/div[2]/div[2]/a[2]'
            ],
            [
                'name' => 'content',
                'type' => 'regex',
                'selector' => '/'
            ]
        ]);
        $this->engine->addRule($rule, 1);
    }

    public function run() {
        $this->engine->start();
    }

    private function createDbFile($dbName) {
        $db = new Db([
            'dsn' => "sqlite:{$dbName}"
        ]);
        $db->query("CREATE TABLE [thread]([title] TEXT, [author] TEXT, [content] TEXT);");
    }

    public function checkRequirement() {
        if (!Environment::checkRequirement()) {
            echo Environment::getInfo();
            exit;
        }
    }
}