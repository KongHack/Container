<?php
// /home/gitlab-runner/builds/qn5sKUTU/0/ace2/ace2-deploy/.git/

$tmp = explode(DIRECTORY_SEPARATOR, __DIR__);

$hunt  = 'ace2-deploy';
$index = array_search($hunt, $tmp);

$instance = $tmp[$index - 2];
$worker   = $tmp[$index - 3];

$binpath = __DIR__.'/vendor/bin/phpstan';
$output  = shell_exec($binpath.' --version');
$output  = trim($output??'');
$tmp     = explode(' ',$output);
$version = array_pop($tmp);

$path = '/tmp/acephpstan/'.$worker.'-'.$instance.'-'.$version.'/';

echo PHP_EOL;
echo 'CONFIGURED TEMP PATH: ',$path,PHP_EOL;
echo PHP_EOL;

if(!is_dir('/tmp/acephpstan')) {
    mkdir('/tmp/acephpstan');
}
if(!is_dir($path)) {
    mkdir($path);
} else {
    echo '!!! WOO, path already exists !!! We might be in the clear here !!! ';
}

$dirs = array_filter(glob('/tmp/acephpstan/*'), 'is_dir');
print_r($dirs);


// Neon files REQUIRE actual tabs...
$contents  = 'parameters:'.PHP_EOL;
$contents .= "\t".'tmpDir: '.$path.PHP_EOL;
$contents .= PHP_EOL;

file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'phpstan.neon.dist', $contents);


