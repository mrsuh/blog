template <class T>
class MyContainer
{
    private:
     T data;

    public:
     void set(T _data) {
        data = _data;
     }
};

int main () {
  MyContainer<int> intContainer;
  intContainer.set(1);

  MyContainer<const char*> stringContainer;
  stringContainer.set("hello");

  return 0;
}
