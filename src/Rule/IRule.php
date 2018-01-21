<?php

namespace loisium\Rule;


interface IRule {
    public function getRuleId(): int;
    public function process(string $source, string &$content, array &$data);
}