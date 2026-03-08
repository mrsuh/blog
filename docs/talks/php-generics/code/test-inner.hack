class MyContainer<reify T> {
    private T $var;

    public function set(T $var): void {
        $this->var = $var;
    }
}

class MyOuterContainer<reify T> {
    private T $var;

    public function set(T $var): void {
        $this->var = $var;
    }
}

<<__EntryPoint>>
function main(): void {
  $MyOuterContainerName = 'MyOuterContainer';
  $intContainer = new $MyOuterContainerName<MyContainer<int>>();
  $myContainer = new MyContainer<int>;
  $myContainer->set(1);
  $intContainer->set($myContainer);

  var_dump($intContainer);
}
