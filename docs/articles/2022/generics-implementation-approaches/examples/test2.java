import java.util.ArrayList;

class Programm2 {
    public static void test(ArrayList<?> list) {
        list.add(1);
    }

    public static void main(String[] args) {
        ArrayList<Integer> intList = new ArrayList<Integer>();
        intList.add(1);

        ArrayList<String> stringList = new ArrayList<String>();
        intList.add("hello");

        test(intList);
        test(stringList);
    }
}


