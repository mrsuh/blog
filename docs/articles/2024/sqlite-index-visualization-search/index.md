# SQLite Index Visualization: Search 

In the previous post, I explained how I learned to extract data from SQLite indexes and visualize it. 
This time, I'll try to visualize a search within an index.


## How does SQLite search within an index?

![](./images/b-tree-sqlite-search.svg)

Inside each page, a binary search occurs among the cell values. After the search, the left child of the nearest cell is chosen. 
If all the cell values on the page are smaller than the searched value, the right child of the page is selected.

If you compile SQLite with [debugging](https://www.sqlite.org/debugging.html) enabled and activate it for queries, you can get a lot of information about SQLite's internal structure and the specific query being run.
First, you should know that SQLite has a virtual machine. This is likely designed to support a wide range of devices on which it can run.
Even with a simple EXPLAIN, we can see the OPCODEs of the virtual machine, its registers (p1, p2...), and comments. You can read more about the virtual machine's internals [here](https://www.sqlite.org/opcode.html).
```sql
EXPLAIN SELECT rowId, column1 FROM table_test INDEXED BY idx WHERE column1 = 1;
addr  opcode         p1    p2    p3    p4             p5  comment      
----  -------------  ----  ----  ----  -------------  --  -------------
0     Init           0     11    0                    0   Start at 11
1     OpenRead       1     2694  0     k(2,,)         2   root=2694 iDb=0; idx
2     Explain        2     0     0     SEARCH table_test USING COVERING INDEX idx (column1=?) 0   
3     Integer        1     1     0                    0   r[1]=1
4     SeekGE         1     10    1     1              0   key=r[1]
5       IdxGT          1     10    1     1              0   key=r[1]
6       IdxRowid       1     2     0                    0   r[2]=rowid; table_test.rowid
7       Column         1     0     3                    0   r[3]= cursor 1 column 0
8       ResultRow      2     2     0                    0   output=r[2..3]
9     Next           1     5     1                    0   
10    Halt           0     0     0                    0   
11    Transaction    0     0     3     0              1   usesStmtJournal=0
12    TableLock      0     2     0     table_test     0   iDb=0 root=2 write=0
13    Goto           0     1     0                    0
```

From the EXPLAIN output, you can see the root page number of the index, the opcodes used to search for data in the index, and the associated values:
```bash
1     OpenRead       1     2694  0     k(2,,)         2   root=2694 iDb=0; idx
...
3     Integer        1     1     0                    0   r[1]=1
4     SeekGE         1     10    1     1              0   key=r[1]
```

After the query runs, you can check the search counter, which will tell you how many times data was searched in the index:
```SQL
SELECT rowId, column1 FROM table_test INDEXED BY idx WHERE column1 = 1;
.testctrl seek_count
1
```

More detailed information about the pages involved and the cells compared cannot be obtained easily. 
I dug into the code and added output to track all the pages and cells read during the search. 
The output looks like this:
```bash
sqlite3DebugMoveToRoot:
sqlite3DebugBtreeIndexMoveto: pageNumber=2694, cellNumber=5, payload=532656, rowId=532656
sqlite3DebugBtreeIndexCompare: index=1, type=int, value=1
sqlite3DebugBtreeIndexMoveto: pageNumber=2694, cellNumber=2, payload=270768, rowId=270768
sqlite3DebugBtreeIndexCompare: index=1, type=int, value=1
...
sqlite3DebugBtreeIndexMoveto: pageNumber=2695, cellNumber=0, payload=1, rowId=1
sqlite3DebugBtreeIndexCompare: index=1, type=int, value=1
sqlite3DebugResultRow:
1|1
```

This gives a detailed trace of the pages and cells involved in the search, allowing us to count the number of basic operations and compare it to the theoretical complexity of the algorithm.
Then I modified the index visualization code, and here's what I came up with:

![](./images/search-equal.webp)

In the upper left corner, we display the general index information and search details: 
* Total number of pages/cells in the index.
* Number of pages loaded for the search.
* Number of cells checked during the search.
* Number of times the data was searched in the index.
* Number of cell comparisons.
* Number of filtered cells.

The searched cell and the cells it linked to are highlighted in bright colors.

It should be clear, when you see the number of pages/cells/comparisons during the search.
However, the number of filtered values needs a bit of explanation. 
For example, you search for a specific number in the index:
```sql
SELECT * FROM table WHERE column = 10;
```
SQLite will first find the first cell with the value 10, and then it will check subsequent cells, as they may also be 10. 
So, we no longer perform a search, but we read the next cells and filter them. 
This means that for simple queries, there should always be at least one filtering operation.

To generate such an image, you'll need a dump of the index and the search. 
This can be done using the following commands:
```bash
docker run -it --rm -v "$PWD":/app/data --platform linux/x86_64 mrsuh/sqlite-index bash
sh bin/dump-index.sh database.sqlite "SELECT column1 FROM table_test INDEXED BY idx WHERE column1 = 1;" dump-index.txt
sh bin/dump-search.sh database.sqlite "SELECT column1 FROM table_test INDEXED BY idx WHERE column1 = 1;" dump-search.txt
php bin/console app:render-search --dumpIndexPath=dump-index.txt --dumpSearchPath=dump-search.txt --outputImagePath=image.webp
```

The search dump file contains a lot of useful information about the query. 
If you want to compare several queries or just see how a query works internally, simply open this file:
```bash
### QUERY
SELECT rowId, column1 FROM table_test INDEXED BY idx WHERE column1 = 1;

### EXPLAIN QUERY PLAN
`--SEARCH table_test USING COVERING INDEX idx (column1=?)

### EXPLAIN QUERY
addr  opcode         p1    p2    p3    p4             p5  comment      
----  -------------  ----  ----  ----  -------------  --  -------------
0     Init           0     11    0                    0   Start at 11
1     OpenRead       1     2694  0     k(2,,)         2   root=2694 iDb=0; idx

...

### RESULT
rowid  column1
-----  -------
1      1      
```

Let’s experiment!

## Query with a single column and equality condition
Before showing each index image, I will describe the table structure, how the index was created, and how the table was populated.

```SQL
CREATE TABLE table_test (column1 INT NOT NULL);
INSERT INTO table_test (column1) VALUES (1),(2),(3),...,(999998),(999999),(1000000);
CREATE INDEX idx ON table_test (column1 ASC);
```
```sql
SELECT rowId, column1 FROM table_test INDEXED BY idx WHERE column1 = 1;
rowid  column1
-----  -------
1      1
```

![](./images/search-equal.webp)

We read 3 pages, compared 19 cells, and filtered out one cell. The actual number of comparisons in our specific example is 19, which is less than the theoretical worst-case complexity of binary search
O(log2(n)) -> O(log2(1.000.000)) -> 19.93

## Testing Queries with Multiple Values in IN()

```SQL
CREATE TABLE table_test (column1 INT NOT NULL);
INSERT INTO table_test (column1) VALUES (1),(2),(3),...,(999998),(999999),(1000000);
CREATE INDEX idx ON table_test (column1 ASC);
```
```sql
SELECT rowId, column1 FROM table_test INDEXED BY idx WHERE column1 IN (1,1000000);
rowid    column1
-------  -------
1        1      
1000000  1000000
```
![](./images/search-range-1000000.webp)

The information on the total search is displayed in the top left, but only the cells involved in finding each value are highlighted. 
For each value in the IN list, SQLite performs a separate index search. In some cases, optimizations may reduce the number of seeks, but in general, 
the DBMS will traverse the index from the root to the target value for each item in the IN list.

## Comparing Searches in ASC/DESC Indices
```SQL
CREATE TABLE table_test (column1 INT NOT NULL);
INSERT INTO table_test (column1) VALUES (1),(2),(3),...,(999998),(999999),(1000000);
CREATE INDEX idx_asc ON table_test (column1 ASC);
CREATE INDEX idx_desc ON table_test (column1 DESC);
```
```sql
SELECT rowId, column1 FROM table_test INDEXED BY idx_asc WHERE column1 IN (1,500000,1000000);
rowid    column1
-------  -------
1        1      
500000   500000 
1000000  1000000
```

![](./images/search-order-asc.webp)

Here, three values are being searched, each requiring an index lookup. 
Fewer filters than lookups are needed since no filtering is necessary after the last value.
 
## Descending Order Search

```sql
SELECT rowId, column1 FROM table_test INDEXED BY idx_desc WHERE column1 IN (1,500000,1000000);
rowid    column1
-------  -------
1000000  1000000
500000   500000 
1        1
```

![](./images/search-order-desc.webp)

As shown, searches in a DESC index work the same way as in an ASC index for specific values.

## Testing Range Searches

```SQL
CREATE TABLE table_test (column1 INT NOT NULL);
INSERT INTO table_test (column1) VALUES (1),(2),(3),...,(999998),(999999),(1000000);
CREATE INDEX idx ON table_test (column1 ASC);
```
```sql
SELECT rowId, column1 FROM table_test INDEXED BY idx WHERE column1 >= 500000 LIMIT 5;
rowid   column1
------  -------
500000  500000 
500001  500001 
500002  500002 
500003  500003 
500004  500004
```

![](./images/search-greater-than.webp)

In this query, the target value was found in 20 comparisons, with 0 filtering, because the search was done on an ascending (ASC) index for a >= comparison. 
SQLite simply read the next several values. There was no need for further comparisons, as all data in the index was already sorted in the required order.

## Expression-Based Searches

```SQL
CREATE TABLE table_test (column1 TEXT NOT NULL);
INSERT INTO table_test (column1) VALUES ('{"timestamp":1}'),('{"timestamp":2}'),('{"timestamp":3}'),...,('{"timestamp":999998}'),('{"timestamp":999999}'),('{"timestamp":1000000}');
CREATE INDEX idx ON table_test (strftime('%Y-%m-%d %H:%M:%S', json_extract(column1, '$.timestamp'), 'unixepoch') ASC);
```
```sql
SELECT rowId, strftime('%Y-%m-%d %H:%M:%S',json_extract(column1, '$.timestamp'), 'unixepoch') AS date FROM table_test INDEXED BY idx WHERE strftime('%Y-%m-%d %H:%M:%S',json_extract(column1, '$.timestamp'), 'unixepoch') = '1970-01-01 00:00:01';
rowid  date               
-----  -------------------
1      1970-01-01 00:00:01
```

![](./images/search-expression.webp)

A search for an exact value formed by an expression, in terms of the number of cells compared, is no different from a simple number search. 
No matter how complex the expression, its index-based search will be fast. 
The most important thing is that the expression in the index exactly matches the expression in the query.

For example, if you have an index like this:
```sql
CREATE INDEX idx ON table_test (column1 + column2);
```
won't work for the query
```sql
SELECT * FROM table_test WHERE (column2 + column1) = 1
```
The expressions are identical in math but not in syntax. Use the exact expression as in the index:
```sql
SELECT * FROM table_test WHERE (column1 + column2) = 1
```

## Let's try searching within a unique index where nearly all values are filled with NULL
```SQL
CREATE TABLE table_test (column1 INT)
INSERT INTO table_test (column1) VALUES (1),(NULL),(NULL),...,(NULL),(NULL),(1000000);
CREATE UNIQUE INDEX idx ON table_test (column1 ASC);
```

```sql
SELECT rowId, column1 FROM table_test INDEXED BY idx WHERE column1 = 1;
rowid  column1
-----  -------
1      1
```

![](./images/search-unique.webp)

This query does not differ from an index without NULL values in terms of the number of cells read.

## Let's try a search in an index without NULL values.

We modified the index slightly, and now it has no NULL values.
```SQL
CREATE TABLE table_test (column1 INT)
INSERT INTO table_test (column1) VALUES (1),(NULL),(NULL),...,(NULL),(NULL),(1000000);
CREATE INDEX idx ON table_test (column1 ASC) WHERE column1 IS NOT NULL;
```
```sql
SELECT rowId, column1 FROM table_test INDEXED BY idx WHERE column1 = 1;
rowid  column1
-----  -------
1      1
```

![](./images/search-partial.webp)

Now, the index contains only the required values, and the query executes instantly!

## Testing a Two-Column Index

```SQL
CREATE TABLE table_test (column1 INT NOT NULL, column2 INT NOT NULL);
INSERT INTO table_test (column1, column2) VALUES (1,1),(2,2),(3,3),...,(999998,999998),(999999,999999),(1000000,1000000);
CREATE INDEX idx ON table_test (column1 ASC, column2 ASC);
```
```sql
SELECT rowId, column1, column2 FROM table_test INDEXED BY idx WHERE column1 = 1 AND column2 = 1;
rowid  column1  column2
-----  -------  -------
1      1        1
```

![](./images/search-complex-equal.webp)

The only difference from an index with a single column is the slightly more complex comparison algorithm.
First, the first column is compared; if it matches the target value, then the second column is compared. If the first column doesn't match the target value, the second column is not compared.
As a result, 19 cells were read, and 20 comparisons were made. This is because the first 18 cells were filtered out based on the first comparison. In the 19th cell, two comparisons were made.

## search-complex-cardinality-equal
Let's try searching for values in an index with two columns, but with different cardinalities. 
The first column has high cardinality, with values ranging from 1 to 1,000,000. The second column has low cardinality, with only values 1 and 2.

We will create two indexes with different column orders.

```SQL
CREATE TABLE table_test (column1 INT NOT NULL, column2 INT NOT NULL);
INSERT INTO table_test (column1, column2) VALUES (1,1),(2,2),(3,1),...,(999998,2),(999999,1),(1000000,2);
CREATE INDEX idx_column1_column2 ON table_test (column1 ASC, column2 ASC);
CREATE INDEX idx_column2_column1 ON table_test (column2 ASC, column1 ASC);
```
```sql
SELECT rowId, column1, column2 FROM table_test INDEXED BY idx_column1_column2 WHERE column1 = 1 AND column2 = 1;
rowid  column1  column2
-----  -------  -------
1      1        1
```

![](./images/search-complex-cardinality-equal-column1.webp)

```sql
SELECT rowId, column1 column2 FROM table_test INDEXED BY idx_column2_column1 WHERE column1 = 1 AND column2 = 1;
rowid  column2
-----  -------
1      1
```

![](./images/search-complex-cardinality-equal-column2.webp)

As shown above, searching in the index for an exact value, where the first column has high cardinality, results in fewer comparisons. 
If you can immediately determine whether the value is suitable, there's no need for a second comparison.

## search-complex-cardinality-greater-than
Let's try searching again using indexes with columns of high and low cardinality, but this time the query will be more complex. 
We will search for an exact match on the column with low cardinality and a range value on the column with high cardinality.

```SQL
CREATE TABLE table_test (column1 INT NOT NULL, column2 INT NOT NULL);
INSERT INTO table_test (column1, column2) VALUES (1,1),(2,2),(3,1),...,(999998,2),(999999,1),(1000000,2);
CREATE INDEX idx_column1_column2 ON table_test (column1 ASC, column2 ASC);
CREATE INDEX idx_column2_column1 ON table_test (column2 ASC, column1 ASC);
```
```sql
SELECT rowId, column1, column2 FROM table_test INDEXED BY idx_column1_column2 WHERE column1 >= 500000 AND column2 = 2  LIMIT 10;
rowid   column1  column2
------  -------  -------
500000  500000   2      
500002  500002   2      
500004  500004   2      
500006  500006   2      
500008  500008   2      
500010  500010   2      
500012  500012   2      
500014  500014   2      
500016  500016   2      
500018  500018   2
```

![](./images/search-complex-cardinality-greater-than-column1.webp)

## search-complex-cardinality-greater-than-column2

```sql
SELECT rowId, column1, column2 FROM table_test INDEXED BY idx_column2_column1 WHERE column1 >= 500000 AND column2 = 2 LIMIT 10;
rowid   column1  column2
------  -------  -------
500000  500000   2      
500002  500002   2      
500004  500004   2      
500006  500006   2      
500008  500008   2      
500010  500010   2      
500012  500012   2      
500014  500014   2      
500016  500016   2      
500018  500018   2
```

![](./images/search-complex-cardinality-greater-than-column2.webp)

From the examples above, it is clear that in this case, it is better to use an index where the column with low cardinality is placed first. 
In our case, after finding the first match, the DBMS can simply select all the following values without additional filtering.
For an index where the column with high cardinality is placed first, finding the first value might be faster (as we saw in the strict search example), 
but filtering the subsequent values based on the second column could be much slower.

All the images generated above can be reproduced using the following commands:
```bash
docker run -it --rm -v "$PWD":/app/data --platform linux/x86_64 mrsuh/sqlite-index bash
sh bin/test-search.sh
```

The code and examples are available [here](https://github.com/mrsuh/sqlite-index)

The visualization of index search operations in SQLite3 provides a better understanding of how this database works at the 
level of internal data structures. We observed that SQLite performs binary search on cells and uses a virtual machine to optimize operations. 
Thanks to detailed logs, we can not only see the number of operations but also the specific steps that lead to finding the desired values. 
The example of different types of queries, such as searching for a single value, a range, or a combination of conditions, 
demonstrates how changes in data structure and indexes can impact search efficiency. 
Ultimately, a deep understanding of SQLite's internal workings helps optimize queries and makes interacting with indexes much more predictable and manageable.
