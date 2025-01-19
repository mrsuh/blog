# RC Boat with ESP8266 NodeMCU

[origin]https://habr.com/ru/articles/513482

![](./images/image-0.jpeg)

I’ll share the entire development process from the very beginning: starting with a boat made of ceiling tiles, a gel pen, and a tin can, to a plastic model that’s good enough to give as a gift.

You can check out the final result here:
<iframe class="rounded" src="https://www.youtube.com/embed/2OwbVLAE5oU?si=AvKhG8UcZBwuWP8w" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

## Why?

Since childhood, I’ve always wanted to build a remote-controlled model. It didn’t matter what kind—what mattered was building it myself.
After discovering Arduino, I started exploring other controllers and came across the ESP8266 NodeMCU. After reading about it, I realized it could help me make this dream a reality.
This started in March. My friend’s birthday was in the summer, so I thought I had plenty of time to build a boat and give it to him as a gift (spoiler: I was a month late). 
He’s a fan of rivers and lakes, so choosing a water vehicle as the model was an easy decision.

## What should the boat be like?

How I imagined the final model:

Electronic components:
* ESP8266 NodeMCU;
* SG90 servo motor for the rudder (simple and affordable);
* Brushless motor (fast and powerful);
* Power bank to power the motor and controller (easy to charge and usable for other purposes);
* 3 LEDs for debugging:
   + Power supply to the controller
   + Client connection to the WebSocket server
   + Command received from the client

Materials:
* Ceiling tiles as the main hull material (easy to work with, inexpensive, and available at any hardware store);
* Epoxy (or something similar) to reinforce the hull later;
* Rudder made from tin (easy to work with and sturdy enough);
* Propeller shaft made from a bicycle spoke, a few bearings, and some tubing (a simple and cheap solution);
* Hot glue gun to assemble everything (because everything is better with a hot glue gun).

I wanted to control the boat from a smartphone, as it's convenient—always charged and readily available.

## Remote Control Prototype 1.0

To start, I built a simple prototype using LEGO, parts from other Arduino projects, and a power bank.

On the ESP8266 NodeMCU, I set up:
* A Wi-Fi access point with a static IP address, allowing connection from a smartphone.
* An HTTP server:
  + Served an HTML page with 5 buttons to control the car.
  + Provided an API for controlling the car via the buttons on the HTML page.

*Connection diagram*
![Connection diagram](./images/image-1.png)

*First prototype*
![First prototype](./images/image-2.jpeg)

*Great, it works!*
<iframe class="rounded" src="https://www.youtube.com/embed/W_7frOsF96s?si=Pztkd3u-Ed5LrUQ5" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

## Remote Control Prototype 2.0

Controlling with buttons wasn’t very convenient, so I redesigned the interface to respond to touch in specific parts of the screen.
With this interface, frequent requests had to be sent to the server, so I added a WebSocket server on the ESP8266 NodeMCU to send commands over an established connection.

*Connection diagram*
![Connection diagram](./images/image-3.png)

*Second prototype*
![Second prototype](./images/image-4.jpeg)

*Control example*
![Control example](./images/image-5.gif)

<iframe class="rounded" src="https://www.youtube.com/embed/eQIDCTf4-K4?si=BHR7rdUEf01EsKzm" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

Detailed instructions on how to build such a car can be found [here](https://vc.ru/dev/160142-rc-mashinka-iz-esp8266-nodemcu-i-lego).

## A Bit of Theory and Materials

Before diving into the details of creating the boat, I should briefly explain its components.

*Image taken from [here](http://sudomodeli.masteraero.ru/kater_model-8.php)*
![](./images/image-6.png)

The main parts that I will be discussing are highlighted in blue:
1 - rudder;
2 - propeller;
3 - drive shaft;
4 - there should be a coupling here;
5 - motor.

**Main materials that inspired me and were used**

Books:
The most useful material I found was in the book by John Finch, "Advanced R/C Boat Modeling." I looked at other books, but this one was the best for me.

Articles:
* <a href="https://www.instructables.com/RC-Boat-2" target="_blank">www.instructables.com/RC-Boat-2</a>
* <a href="http://www.parkflyer.ru/ru/blogs/view_entry/600" target="_blank">www.parkflyer.ru/ru/blogs/view_entry/600</a>

Video course [how to make a rocket boat](https://www.youtube.com/watch?v=dFc1KUHF7-s&list=PLGL_lVjhxY8eKivkT19brQz_dBuW9ueWr)

## Testing Reception Range

To test the range of the control system, I built this device:

![](./images/image-7.jpeg)

![](./images/image-8.jpeg)

On the client side, you can run a `ping/pong` test to the WebSocket server with response time measurement.
The table displays the number of requests/responses and the response time percentiles.

After walking around the park with direct line of sight, I found that the maximum distance I could maintain a reliable connection between the device and my smartphone was about 27 meters (I later checked the distance on the map).
27 meters: 95th percentile ~ 48 milliseconds, 99th percentile ~ 283 milliseconds.

*Client interface for testing reception range*
![Client interface for testing reception range](./images/image-9.jpeg)

[Here](https://github.com/mrsuh/esp8266-distance-test) is the link to get the code and run the experiment yourself.

## Version 1.0

Electronic components:
* ESP8266 NodeMCU;
* L298N;
* SG90 Servo;
* TT130 brushed motor;
* powerbank;
* three-color LED.

Materials:
* Ceiling tile for the hull;
* Rudder made from a can;
* A drive shaft made from a gel pen and wooden skewer, glued with a hot glue gun;
* Propeller bought on AliExpress.

Version features:
* The rudder is placed very close to the hull of the boat;
* The angle of the main shaft relative to the hull is quite large, and the drive shaft goes under the bottom of the boat;
* The drive shaft leaks badly.

I already had a working remote control scheme, so I could start working on the hull.
To assemble the boat's hull, I used blueprints from an article by [Francisco Moliner](https://www.instructables.com/id/RC-Boat-2).
I printed them, glued them together, and cut them out of ceiling tile.

![](./images/image-10.jpeg)

While I was assembling the hull and thinking about what to make the drive shaft from, my brushless motor A2212 1000KV arrived.

In my inexperience, I ordered a motor for drones:
* Not only the motor shaft spins, but the whole motor rotates relative to the base (how to mount it to the boat?);
* A specific motor controller.

I struggled with this motor for a week.
I managed to start the motor, but when the voltage changed quickly (rapidly increasing or decreasing speed), the motor would turn off and the controller would restart.
I think this was due to the low power of the power supply (the powerbank).
In the end, I decided to switch to a more powerful brushed motor.

*Trying in vain to set up stable motor operation*
![Trying in vain to set up stable motor operation](./images/image-11.jpeg)

*Assembly is in full swing*
![Assembly is in full swing](./images/image-12.jpeg)

First test launch
<iframe class="rounded" src="https://www.youtube.com/embed/MnyuY5TDpVY?si=29HsVZbohLljlNK9" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

I realized that I could make a good prototype, but it wouldn't look presentable.
I decided it was time to order a 3D printer, which I had wanted to try for a while.

## Version 1.1

Version features:
* Added a roof to prevent water from getting into the boat from the top (still sealed the top with tape during tests);
* Moved the rudder a bit away from the hull to improve control;
* Reduced the tilt angle of the main shaft to increase the boat's power;
* The drive shaft is made from a gel pen, an umbrella rod, and technical oil (almost no leaks);
* Replaced the L298N with an L298N mini (smaller size, no significant difference in this version);
* The boat's speed is not adjustable;
* The new R280 motor (3-12v, 5000-15000 rpm) is much more powerful than the previous one.

To control the motor speed, I used the L298N, but lost almost half of the power.
This is a feature of using PWM or the way the circuit is designed — I'm not exactly sure.
I decided to abandon speed control. In the end, I used the L298N, but without PWM control, which significantly increased the motor power.

## First Launch

Unfortunately, I chose the closest pond, which was very overgrown.
After swimming just one and a half meters, the boat tangled weeds around the propeller shaft and stopped responding to control.

<iframe class="rounded" src="https://www.youtube.com/embed/64bB_-jc_tM?si=sTP0RuQPT-TruFQu" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
After ten minutes, using a few branches taped together into one large one, we managed to retrieve the boat from the water.
Here are the first photos of the boat, but after that swim:

*Not the cleanest pond*
![Not the cleanest pond](./images/image-13.jpeg)

*It's easy to see the positions of the rudder and the propeller*
![It's easy to see the positions of the rudder and the propeller](./images/image-14.jpeg)

*You can see how the weeds tangled around the shaft*
![You can see how the weeds tangled around the shaft](./images/image-15.jpeg)

The boat still leaked a bit, so I had to search for the leak.

*The blue goo is clearly visible on the white background*
![The blue goo is clearly visible on the white background](./images/image-16.jpeg)

## Version 1.2

Version features:
* Installed a protection for the propeller from weeds;
* Increased the surface area of the rudder;
* Changed the pond.

*The boat, cleaned of dirt, looks much better*
![The boat, cleaned of dirt, looks much better](./images/image-17.jpeg)

*The rudder surface area is increased*
![The rudder surface area is increased](./images/image-18.jpeg)

*Weed protection added*
![Weed protection added](./images/image-19.jpeg)

First successful launch:
<iframe class="rounded" src="https://www.youtube.com/embed/NQefcdBtruM?si=b5vxgf6OLzmIPpVA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

## Version 1.3

Version features:
* The boat's length was reduced by half.

Suddenly, I realized that the boat would require a lot of plastic, so I decided to make it a bit shorter.
To see how the shortened version would look, I redesigned the existing hull.

<iframe class="rounded" src="https://www.youtube.com/embed/H2KYk2DC7Jw?si=YFBjeMVJPuaJMr_z" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

## Version 2.0

Electronic components:
* ESP8266 NodeMCU;
* L298N;
* Servo SG90;
* Brushed motor R370 3-12v 10000-41000 rpm;
* Powerbank;
* Three-color LED.

Materials:
* Ceiling tile for the hull;
* Plastic rudder;
* Plastic shaft, umbrella rod, and bearings for the shaft.

Version features:
* Designed and 3D printed the rudder, shaft, and coupling;
* Used a new motor R370 3-12v 10000-41000 rpm;
* Moved the propeller and rudder further from the boat;
* Assembled the hull with new dimensions;
* Removed the propeller algae guard.

The 3D printer arrived!

*The package from China arrived in perfect condition*
![The package from China arrived in perfect condition](./images/image-20.jpeg)

*The assembly process took about 6 hours*
![The assembly process took about 6 hours](./images/image-21.jpeg)

*Test print*
![Test print](./images/image-22.jpeg)

*Printing the shaft and coupling*
![Printing the shaft and coupling](./images/image-23.jpeg)

![](./images/image-24.jpeg)

*The new hull looked neat*
![The new hull looked neat](./images/image-25.jpeg)

It works great!
<iframe class="rounded" src="https://www.youtube.com/embed/awhC4guPnek?si=WvRaZom34xKZBmRd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

It was unclear what to do with the hole where water was getting in:

![](./images/image-26.jpeg)

![](./images/image-27.jpeg)

## Version 3.0

Electronics components:
* ESP8266 NodeMCU;
* L298N;
* Servo SG90;
* Brushed motor R370 3-12v 10000-41000 rpm;
* Powerbank;
* 3 LEDs.

Materials:
* Plastic body;
* Plastic rudder;
* Plastic shaft, umbrella rod, and technical oil for the drive shaft.

Version features:
* The entire body is made of plastic.

Before this, I had only worked in Kompas 3D for modeling simple designs, but the principles in Fusion360 are quite similar. I had to spend a few weekends learning how to optimize modeling in this program. After a couple of weeks, the first usable version of the boat body model was ready!

![](./images/image-28.png)

![](./images/image-29.png)

![](./images/image-30.png)

![](./images/image-31.png)

I divided the body into several parts and started printing. 
I didn’t focus much on the printing settings, so the quality isn’t great.

![](./images/image-32.jpeg)

*Bow of the boat*
![Bow of the boat](./images/image-33.jpeg)

![](./images/image-34.jpeg)

*Stern of the boat*
![Stern of the boat](./images/image-35.jpeg)

![](./images/image-36.jpeg)

*Top of the boat*
![Top of the boat](./images/image-37.jpeg)

*Assembly process of the body*
![Assembly process of the body](./images/image-38.jpeg)

![](./images/image-39.jpeg)

*Completed body*
![Completed body](./images/image-40.jpeg)

Additionally, I modeled and printed the rudder. I solved the issue with the hole for controlling the rudder (visible in the photo). As a result, water almost stopped entering the boat through it.

*One of the final versions*
![One of the final versions](./images/image-41.png)

![](./images/image-42.jpeg)

![](./images/image-43.jpeg)

![](./images/image-44.jpeg)

*Replaced one RGB LED with three different LEDs*
![Replaced one RGB LED with three different LEDs](./images/image-45.jpeg)

The new motor occasionally didn't start on the first try, which was really frustrating. I thought this might be due to the power supply (powerbank), so I experimented with regular AA batteries, which seemed to improve things.

*Experimenting with AA batteries*
![Experimenting with AA batteries](./images/image-46.jpeg)

Then I suddenly realized that the powerbank probably contains multiple batteries, and I might be able to connect them in the way I need. So, I opened up the powerbank... but it turned out there was only one battery inside.

*Only one battery inside the powerbank*
![Only one battery inside the powerbank](./images/image-47.jpeg)

So, I decided I needed to order some batteries.

## Version 3.1

Version features:
* Replaced the powerbank with 4 x 18650 batteries (2 for the controller and 2 for the motor).
* Designed and printed battery compartments.
* The motor now works reliably!

Here are the new battery compartments:
![](./images/image-48.png)

![](./images/image-49.jpeg)

*The contacts are still made from tin cans*
![The contacts are still made from tin cans](./images/image-50.jpeg)

![](./images/image-51.jpeg)

*The compartments have been glued into the boat*
![The compartments have been glued into the boat](./images/image-52.jpeg)

Finally, everything is working smoothly!

## Version 3.2

Version features:
* The shaft in the drive leg was replaced with a bicycle spoke (larger diameter and length).

Here are the updates:

![](./images/image-53.jpeg)

![](./images/image-54.jpeg)

*It wasn't easy to replace the already glued-in drive leg*
![It wasn't easy to replace the already glued-in drive leg](./images/image-55.jpeg)

## Version 3.3

Version features:
* The coupling was redesigned into a more flexible version.
* The L298N was reinstalled, as it should handle higher currents.

*Part of the flexible coupling*
![Part of the flexible coupling](./images/image-56.png)

![](./images/image-57.jpeg)

![](./images/image-58.jpeg)

*Complete set of the new version*
![Complete set of the new version](./images/image-59.jpeg)

![](./images/image-60.jpeg)

*All parts of the boat and main components are made from plastic!*
![All parts of the boat and main components are made from plastic!](./images/image-61.jpeg)

First water test of version 3.x:
<iframe class="rounded" src="https://www.youtube.com/embed/Si9PfFbOyKs?si=y_APvv91P5eQX43t" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

During real-life tests, it became clear that the distance at which a stable connection between the smartphone and the boat could be maintained was about three times shorter than during tests (27 meters vs ~10 meters). The connection was often lost in the middle of the pond, and we had to wait for the wind to push the boat back to shore for reconnection. After these issues, an Emergency timeout parameter was added, which can be set on the client (default is 4 minutes). If no command was sent within this timeout, the boat would slowly move forward, helping avoid the boat drifting too far away. This strategy worked well on a small pond, but for larger bodies of water, this timeout should be removed.

The brushless motor was reintroduced after trying it with 18650 batteries, and it worked as expected. However, since the brushless motor is designed for drones, it could not simply be mounted horizontally, as the motor rotates around its base. So, a custom mount was modeled and printed for it:

![](./images/image-62.jpeg)

*First version of the base*
![First version of the base](./images/image-63.jpeg)

*The base fits perfectly*
![The base fits perfectly](./images/image-64.jpeg)

The first version of the motor mount fit the motor perfectly but didn't fit well inside the boat, so further adjustments were needed. After 2-3 iterations of printing, the final mount was created that fit perfectly inside the boat and allowed the motor to work without interference:

![](./images/image-65.png)

*The entire shaft assembly from motor to propeller*
![The entire shaft assembly from motor to propeller](./images/image-66.jpeg)

## Version 3.4

Version features:
* Installed a brushless motor A2212 1000KV.
* Modeled and printed a custom mount for the motor.

*Glued the motor in and tested it separately*
![Glued the motor in and tested it separately](./images/image-67.jpeg)

*Complete set of the new version*
![Complete set of the new version](./images/image-68.jpeg)

![](./images/image-69.jpeg)

*Assembled version*
![Assembled version](./images/image-70.jpeg)

Weighed the boat in full configuration:
*The boat's full weight with all components is 626 grams*
![The boat's full weight with all components is 626 grams](./images/image-71.jpeg)

## Version 3.5

Version features:
* Glued the top with rubber bands to ensure the lid fits tightly.
* Added two more holes for screws in the lid, again to ensure a tight fit.
* Secured the motor and battery mounting areas with super glue.

![](./images/image-72.jpeg)

*Glued the top with rubber bands*
![Glued the top with rubber bands](./images/image-73.jpeg)

Final version:

![](./images/image-74.jpeg)

![](./images/image-75.jpeg)

![](./images/image-76.jpeg)

Final video of version 3.5
<iframe class="rounded" src="https://www.youtube.com/embed/2OwbVLAE5oU?si=ojQu8Cci6axYGNkr" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
<iframe class="rounded" src="https://www.youtube.com/embed/JPhXwM6JG8E?si=C2vpp5isUWBNnxxg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

## Client UI
![](./images/image-77.jpeg)

When the settings menu is collapsed, nearly the entire screen area is available for control.

Settings:
* **Auto move** (on/off) — When enabled, the speed is fixed and set by the Speed parameter.
* **Speed** (%) — Used in conjunction with the Auto move parameter.
* **Move interval** (ms) — The interval at which commands will be sent from the client. We cannot send commands too infrequently, as this will cause a large delay in boat control. Conversely, too small of a delay may overwhelm the controller, so an optimal delay is necessary. Default: 50 milliseconds.
* **Move timeout** (ms) — The interval after which the boat will assume no new commands are coming. Some commands arrive with a delay, meaning there are pauses longer than the Move interval between them. To keep movement smooth, the Move timeout parameter is used. The boat will continue the last command for Move timeout milliseconds. Default: 600 milliseconds.
* **Emergency timeout** (ms) — If no commands are received within this timeout, the boat will begin moving slowly forward. Default: 4 minutes.
* **Debug** (on/off) — Enables debugging, displaying detailed connection errors and all commands.

All timeouts are sent to the server with each command, so they can be adjusted at any time.

*Direction control*
![Direction control](./images/image-78.gif)

*Speed control*
![Speed control](./images/image-79.gif)

## Conclusion

The entire process took about six months. The model could have been refined and improved even further, but I decided to stop at the current result.
My friend liked the gift, so I’m doubly satisfied!

![](./images/image-80.jpeg)

![](./images/image-81.jpeg)

The source code for the 3D models and the boat code can be found [here](https://github.com/mrsuh/boat-esp8266). 
The source code for the distance test is available [here](https://github.com/mrsuh/esp8266-distance-test).

Thanks for your attention!
