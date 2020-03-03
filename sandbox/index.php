<?php
/**
 * @Param int $min_ms Minimum amount of time in milliseconds that it should take
 * to calculate the hashes
 */
function getOptimalBcryptCostParameter($min_ms = 250) {
    for ($i = 4; $i < 31; $i++) {
        $options = [ 'cost' => $i, 'salt' => 'usesomesillystringforsalt' ];
        $time_start = microtime(true);
        password_hash("rasmuslerdorf", PASSWORD_BCRYPT, $options);
        $time_end = microtime(true);
        if (($time_end - $time_start) * 1000 > $min_ms) {
            return $i;
        }
    }
}
echo getOptimalBcryptCostParameter(); // prints 12 in my case