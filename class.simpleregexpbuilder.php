<?php

class SimpleRegexpBuilder {

    protected $p = '';
    public $opts = array('i' => true, 'e' => 'utf8', 'd' => '/', 'b' => true, 'r' => false); /* i: not case sensitive, e: encoding of string in array, d: delimiter, start and end character, b: bounary, r: created pattern will be reusable */
    protected $_u = ''; # i18n need
    protected $esc = '[\^$.|?*+()';

    public function __construct(array $words, array $opts = array()) {
        $this->opts = $opts + $this->opts;
        $this->esc .= $this->opts['d'];
        $this->p = $this->opts['d'] . ($this->opts['b'] ? '\b' : '') . $this->g(call_user_func_array('array_merge_recursive', array_map(array($this, 't'), $words))) . ($this->opts['b'] ? '\b' : '') . $this->opts['d'] . $this->_u . ($this->opts['i'] ? 'i' : '') . ($this->opts['r'] ? 'S' : '');
    }

    protected function t($s) {
        $c = mb_substr($s, 0, 1, $this->opts['e']);
        if (strlen($c) > 1)
            $this->_u = 'u';
        else if ($this->opts['i'])
            $c = strtolower($c);
        return array($c => mb_strlen($s, $this->opts['e']) > 1 ? $this->t(mb_substr($s, 1, mb_strlen($s, $this->opts['e']), $this->opts['e'])) : array('' => ''));
    }

    protected function g(array $t, $x = 0) {
        $r = array();
        $i = 0;
        foreach ($t as $c => $a)
            if ($c != '')
                $r[] = (strpos($this->esc, $c) === false ? '' : '\\') . $c . ($a == array('' => '') ? '' : (++$i ? $this->g($a, $x + 1) : ''));
        $b = isset($t['']);
        return ((count($r) > 1 || $i && $b) ? '(' . ($x ? '?:' : '') . join('|', $r) . ')' : ($x ? $r[0] : '(' . $r[0] . ')')) . ($b ? '?' : '');
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