<?php
$code = file_get_contents($argv[1]);

$tokens = PhpToken::tokenize($code);

foreach ($tokens as $token) {
    echo $token->getTokenName() . " lexer.php";
}
