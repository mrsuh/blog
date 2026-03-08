class Container<T> {

  private T data;

  public void set(T data) {
      this.data = data;
  }
}

class GlobalContainer<T> {

  private T data;

  public void set(T data) {
      this.data = data;
  }
}

class Programm {
    public static void main(String[] args) {
        GlobalContainer<Container<Integer>> intContainer = new GlobalContainer<Container<Integer>>();
        Container<Integer> c = new Container<Integer>();
        c.set(1);
        intContainer.set(c);
    }
}


