<?php

namespace loisium\Cache;

interface ICache {
    public function set($key, $value);
    public function get($key);
    public function push($stack, $value);
    public function pop($stack);
    public function count($stack);
}