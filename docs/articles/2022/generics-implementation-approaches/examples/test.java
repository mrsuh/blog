class Container<T> {

  private T data;

  public void set(T data) {
      this.data = data;
  }
}

class Programm {
    public static void main(String[] args) {
        Container<Integer> intContainer = new Container<Integer>();
        intContainer.set("hello");

        Container<String> stringContainer = new Container<String>();
        stringContainer.set("hello");
    }
}


