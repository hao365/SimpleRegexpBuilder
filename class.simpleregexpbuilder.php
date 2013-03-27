<?php

class SimpleRegexpBuilder {

    private $pattern = '';
    private $opts = array('i' => true, 'encoding' => 'utf8', 'delimiter' => '/', 'b' => true); /* i: not case sensitive, encoding: string in array encoding, delimiter: start and end character, b: bounary */
    private $_u = ''; # i18n need

    public function __construct(array $words, array $opts = array()) {
        $this->opts = $opts + $this->opts;
        $this->pattern = $this->opts['delimiter'] . ($this->opts['b'] ? '\b' : '') . substr($this->g(call_user_func_array('array_merge_recursive', array_map(array($this, 't'), $words))), 3, -1) . ($this->opts['b'] ? '\b' : '') . $this->opts['delimiter'] . $this->_u . ($this->opts['i'] ? 'i' : '') . 'S';
    }

    private function t($str) {
        $c = mb_substr($str, 0, 1, $this->opts['encoding']);
        if (strlen($c) > 1)
            $this->_u = 'u';
        else if ($this->opts['i'])
            $c = strtolower($c);
        return array($c => mb_strlen($str, $this->opts['encoding']) > 1 ? $this->t(mb_substr($str, 1, mb_strlen($str, $this->opts['encoding']), $this->opts['encoding'])) : array('NULL' => ''));
    }

    private function g(array $tree, $level = 0) {
        $ret = array();
        foreach ($tree as $node => $cnode)
            if ($node != 'NULL')
                $ret[] = $node . ($cnode == array('NULL' => '') ? '' : $this->g($cnode, $level + 1));
        $b = isset($tree['NULL']);
        return ((count($ret) > 1 || (mb_strlen($ret[0], $this->opts['encoding']) > 1 && $b) || !$level ? '(?:' . join(($this->opts['b'] && !$level ? '\b|\b' : '|'), $ret) . ')' : $ret[0]) . ($b ? '?' : ''));
    }

    public function get() {
        return $this->pattern;
    }

    public function match($str, &$m) {
        return preg_match($this->pattern, $str, $r) ? count($m = $r[0]) : 0;
    }

    public function matchAll($str, array &$m) {
        return preg_match_all($this->pattern, $str, $ret) ? count($m = $ret[0]) : 0;
    }

}