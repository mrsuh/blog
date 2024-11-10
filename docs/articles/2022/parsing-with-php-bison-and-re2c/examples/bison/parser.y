%code top {
  #include <ctype.h>  /* isdigit. */
  #include <stdio.h>  /* printf. */

  int yylex();
  void yyerror(char const *);
}

%define api.header.include {"parser.h"}

%define api.value.type {double}
%token TOK_NUMBER
%type  expr

%left '-' '+'

%% /* The grammar follows. */
input:
  %empty
| expr '\n'  { printf ("%.10g\n", $1); }
;

expr:
  TOK_NUMBER    { $$ = $1; }
| expr '+' expr { $$ = $1 + $3; }
| expr '-' expr { $$ = $1 - $3; }
;

%%

int yylex()
{
    int c;

    /* Ignore white space, get first nonwhite character.  */
    while ((c = getchar()) == ' ' || c == '\t')
    {
        continue;
    }

    if (c == EOF)
    {
        return YYEOF;
    }

    /* Char starts a number => parse the number. */
    if (c == '.' || isdigit(c))
    {
        ungetc(c, stdin);
        if (scanf("%lf", &yylval) != 1)
        {
            return YYUNDEF;
        }

        return TOK_NUMBER;
    }

    return c;
}

void yyerror(char const *s)
{
    fprintf(stderr, "%s\n", s);
}

int main()
{
    return yyparse();
}
