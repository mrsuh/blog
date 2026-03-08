class MyContainer<reify T> {
    private T $var;

    public function set(T $var): void {
        $this->var = $var;
    }
}

<<__EntryPoint>>
function main(): void {
  $intContainer = new MyContainer<int>();
  if(false) {
    $intContainer->set("hello");
  }


  $stringContainer = new MyContainer<string>();
  $stringContainer->set("hello");
}
