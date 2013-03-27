<?php

require './class.simpleregexpbuilder.php';

$arr = array(
    array('abc', 'ab', 'abd', 'bcd', 'aca', 'bca', 'bc'),
    array('xxxy', 'xy', 'yyy', 'x', 'y'),
    array('北京', '京', '上', '上海'),
    array('b', 'ab'),
    array('a', 'ab')
);

foreach ($arr as $a) {
    $m = new SimpleRegexpBuilder($a, array('b' => false));
    $reg = $m->get();
    echo $reg . PHP_EOL;
}
