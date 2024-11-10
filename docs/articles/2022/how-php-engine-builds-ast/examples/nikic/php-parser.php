<?php

require_once __DIR__ . '/../vendor/autoload.php';

use parser\lib\PhpParser\Error;
use parser\lib\PhpParser\NodeDumper;
use parser\lib\PhpParser\ParserFactory;

$code = <<<'CODE'
<?php

$a = 1;

echo $a;
CODE;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
try {
    $ast = $parser->parse($code);
} catch (Error $error) {
    echo "Parse error: {$error->getMessage()}\n";
    return;
}

$dumper = new NodeDumper;
echo $dumper->dump($ast) . "\n";
