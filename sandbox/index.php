<?php
declare(strict_types=1);

\Phalcon\Helper\Json::decode()
exit;

$a = new \stdClass();
$b = new \stdClass();

echo spl_object_id($a) . PHP_EOL;
echo spl_object_id($b) . PHP_EOL;

exit;
$a1 = md5( md5('K') . md5('U') . md5('C') . md5('O') . md5('I') . md5('N')  );
$a2 = md5( md5('2') . md5('0') . md5('2') . md5('0') );
$a3 = md5(md5('I') . md5('R') . md5('I') . md5('N') . md5('A'));

echo md5($a1 . $a2 . $a3) . PHP_EOL;