<?php
/*
 * Author: fq.gao@hao365.com
 * GPL License
 */
class SimpleRegexpBuilder {

    public $opts = array('i' => true, 'e' => 'UTF-8', 'd' => '/', 'b' => true, 'r' => false); /* i: not case sensitive, e: encoding of string in array, d: delimiter, b: bounary, r: the pattern will be reusable */
    protected $p = '';
    protected $_u = ''; # i18n need
    protected $esc = '[\^$.|?*+()';

    public function __construct(array $words, array $opts = array()) {
        $this->opts = $opts + $this->opts;
        $this->esc .= $this->opts['d'];
        $this->p = $this->opts['d'] . ($this->opts['b'] ? '\b' : '') . $this->g(call_user_func_array('array_merge_recursive', array_map(array($this, 't'), $words)), 0) . ($this->opts['b'] ? '\b' : '') . $this->opts['d'] . $this->_u . ($this->opts['i'] ? 'i' : '') . ($this->opts['r'] ? 'S' : '');
    }

    protected function t($s) {
        $c = mb_substr($s, 0, 1, $this->opts['e']);
        $l = strlen($c);
        if ($l > 1)
            $this->_u = 'u';
        else if ($this->opts['i'])
            $c = strtolower($c);
        return array($c => strlen($s) > $l ? $this->t(substr($s, $l)) : array('' => ''));
    }

    protected function g(array $t, $x) {
        $r = array();
        $i = 0;
        foreach ($t as $c => $a)
            if ($c != '')
                $r[] = (strpos($this->esc, $c) === false ? '' : '\\') . $c . ($a == array('' => '') ? '' : $this->g($a, ++$i + $x));
        $b = isset($t['']);
        return (count($r) > 1 || $i && $b ? '(' . ($x ? '?:' : '') . join('|', $r) . ')' : ($x ? $r[0] : '(' . $r[0] . ')')) . ($b ? '?' : '');
    }

    public function getRegexp() {
        return $this->p;
    }

    public function match($s, &$m) {
        return preg_match($this->p, $s, $m);
    }

    public function matchAll($s, array &$m) {
        return preg_match_all($this->p, $s, $r) ? count($m = $r[0]) : 0;
    }

}