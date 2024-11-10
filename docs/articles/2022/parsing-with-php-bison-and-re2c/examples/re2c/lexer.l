#include <stdio.h>

const char *lex(const char *s)
{
    /*!re2c
        re2c:yyfill:enable = 0;
        re2c:define:YYCTYPE = char;
        re2c:define:YYCURSOR = s;

        [0-9]+ { return "TOK_NUMBER"; }
        *      { return "TOK_ANY"; }
    */

    return "";
}

int main(int argc, char *argv[])
{
    printf("%s\n", lex(argv[1]));

    return 0;
}
