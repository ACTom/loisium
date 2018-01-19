<?php

namespace loisium\Rule;


class ListRule implements IRule {
    private $ruleId = 2;

    public function getRuleId(): int {
        return $this->ruleId;
    }

    public function process(string &$source, &$data) {

    }
}