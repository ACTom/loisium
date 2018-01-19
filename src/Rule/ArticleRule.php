<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 18-1-19
 * Time: 下午8:56
 */

namespace loisium\Rule;


class ArticleRule implements IRule {
    private $ruleId = 1;

    public function getRuleId(): int {
        return $this->ruleId;
    }

    public function process(string &$source, &$data) {

    }

}