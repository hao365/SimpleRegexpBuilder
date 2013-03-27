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
    echo $m->getRegexp() . PHP_EOL;
}

echo PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;

echo '============ INPUT ============' . PHP_EOL;
$keywords = array('fox', 'fax', 'god', 'dog', 'doggie', 'abc', 'hover', 'the', 've');
$text = 'The quick brown fox jumps over a lazy dog';
$m = new SimpleRegexpBuilder($keywords, array('i' => true, 'b' => true));
echo "Text:\t\t" . $text . PHP_EOL;
echo "List:\t\t" . join(' ', $keywords) . PHP_EOL;
echo "Regexp:\t\t" . $m->getRegexp() . PHP_EOL;
echo "Options:" . var_export($m->opts, 1) . PHP_EOL . PHP_EOL;
$matches = array();
echo '============ OUTPUT ===========' . PHP_EOL;
echo "Count:\t\t" . $m->matchAll($text, $matches) . PHP_EOL;
echo "Result:" . print_r($matches, 1) . PHP_EOL;
