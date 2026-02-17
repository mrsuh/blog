# XOR Gate using transistors

The [XOR Gate](https://en.wikipedia.org/wiki/XOR_gate) is a basic digital logic gate that implements the logical **XOR** function. It has two inputs and one output.
The XOR gate can be built using a combination of [AND](/articles/2026/and-gate-using-transistors/), [NAND](/articles/2026/nand-gate-using-transistors/), and [OR](/articles/2026/or-gate-using-transistors/) Gates. 
The full truth table showing all input and output states can be found below. Each row represents a physical state of the circuit.

### Truth table

| Input A | Input B | Output |
|---------|---------|--------|
| 0       | 0       | 0      |
| 0       | 1       | 1      |
| 1       | 0       | 1      |
| 1       | 1       | 0      |

### Logic scheme

The inputs and output in the scheme correspond to the truth table headers.

![](./images/scheme.svg)

### Implementation

For the implementation, NPN 2N2222A transistors were used.

![](./images/view.webp)

|
:---:|:---:
![](./images/front.webp) | ![](./images/back.webp)


The video demonstrates the circuit behavior for all input states.

<iframe class="rounded" src="https://youtube.com/embed/Yx4fcMg7LD0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
