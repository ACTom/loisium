<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 18-1-19
 * Time: 下午8:56
 */

namespace loisium\Rule;


use DOMDocument;
use DOMXPath;
use loisium\Engine;

class ArticleRule implements IRule {
    const LIST_URL = 1;
    const ARTICLE_URL = 2;
    private $ruleId = 1;
    private $engine;
    private $listRegex = '';
    private $articleRegex = '';
    private $source = '';
    private $content = '';
    private $xpath = null;


    public function __construct(Engine $engine) {
        $this->engine = $engine;
    }

    public function getRuleId(): int {
        return $this->ruleId;
    }

    public function process(string $source, string &$content, &$data) {
        $this->source = trim($source);
        $this->content = $content;

        $doc = new DOMDocument;
        $doc->preserveWhiteSpace = false;
        $doc->LoadHTML($content);
        $this->xpath = new DOMXPath($doc);

        $this->parseUrl();
    }

    private function extendUrl(string $url) {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        /* 去除掉javascript和本页地址 */
        if(preg_match("/^(javascript:|#|'|\")/i", $url)) {
            return false;
        }

        $parseSource = parse_url($this->source);

        /* 以//开头的补充协议 */
        if (strpos($url, '//') === 0) {
            $url = $parseSource['scheme'] . ':' . $url;
        }

        /* 获取基本地址 */
        $baseHostUrl = $parseSource['scheme'] . '://';
        if (isset($parseSource['user'])) {
            $baseHostUrl .= $parseSource['user'];
            if (isset($parseSource['pass'])) {
                $baseHostUrl .= ':' . $parseSource['pass'];
            }
            $baseHostUrl .= '@';
        }
        $baseHostUrl .= $parseSource['host'];
        if (isset($parseSource['port'])) {
            $baseHostUrl .= ':' . $parseSource['port'];
        }

        /* 获取当前url目录地址 */
        $hostArray = explode('/', $parseSource['path']);
        array_pop($hostArray);
        $baseDirectory = implode('/', $hostArray);

        /* 以/开头的补全协议和主机 */
        if (strpos($url, '/') === 0) {
            $url = $baseHostUrl . $url;
        }

        $parseUrl = parse_url($url);
        if (!isset($parseUrl['host'])) {
            $url = $baseHostUrl . $baseDirectory . '/' . $url;
        }

        return $url;
    }

    private function conformUrl(string $url) {
        if (preg_match($this->listRegex, $url)) {
            return self::LIST_URL;
        }

        if (preg_match($this->articleRegex, $url)) {
            return self::ARTICLE_URL;
        }

        return false;
    }

    private function parseUrl() {
        $urls = $this->xpath->query('//a/@href');
        if (empty($urls)) {
            return ;
        }
        foreach ($urls as $url) {
            $url = $this->extendUrl($url);
            if ($url !== false && $this->conformUrl($url)) {
                $this->engine->pushQueue($url);
            }
        }

    }

    /**
     * @return string
     */
    public function getListRegex(): string {
        return $this->listRegex;
    }

    /**
     * @param string $listRegex
     */
    public function setListRegex(string $listRegex) {
        $this->listRegex = $listRegex;
    }

    /**
     * @return string
     */
    public function getArticleRegex(): string {
        return $this->articleRegex;
    }

    /**
     * @param string $articleRegex
     */
    public function setArticleRegex(string $articleRegex) {
        $this->articleRegex = $articleRegex;
    }

}