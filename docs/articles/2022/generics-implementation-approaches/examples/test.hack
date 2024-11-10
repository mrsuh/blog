class MyContainer<reify T> {
    private T $var;

    public function set(T $var): void {
        $this->var = $var;
    }
}

<<__EntryPoint>>
function main(): void {
  $intContainer = new MyContainer<int>();
  $intContainer->set(1);

  $stringContainer = new MyContainer<string>();
  $stringContainer->set("hello");

  var_dump($intContainer);
  var_dump($stringContainer);
}
