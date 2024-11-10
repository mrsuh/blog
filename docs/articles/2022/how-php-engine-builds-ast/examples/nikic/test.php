<?php

require __DIR__ . '/util.php';

$code = <<<'EOC'
<?php

$a = 1;

echo $a;
EOC;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";
