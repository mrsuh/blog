# Telegram bot that monitors currency availability in Tinkoff ATMs

[origin]https://vc.ru/u/585016-anton-sukhachev/393167-telegram-bot-kotoryi-sledit-za-valyutoi-v-bankomatah-tinkoff

Recently, many people have faced issues withdrawing foreign currency from Tinkoff ATMs. I, too, regularly checked currency availability through the official app, but it didn’t help much—by the time I reached the ATM, the money was already gone. So, I decided to monitor only a few nearby ATMs within walking distance. To make tracking currency availability easier, I created a bot that can send notifications when the desired currency becomes available at a specific ATM.

Every ATM in the country has a unique identifier, which you can find on Tinkoff's official ATM map. Using this identifier, you can set up alerts. Once the specified amount of currency becomes available at the ATM, you’ll receive a notification.

<iframe class="rounded" src="https://youtube.com/embed/QEqb0XdcW0Q?si=CJKqrTQS8sdYg2gM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

This bot has already helped me, so I decided to share it with others. 

For now, I’m not planning to add more features or monetize it. If you'd like to create a similar bot, you can find the source code [here](https://github.com/mrsuh/tinkoff-atm-bot). 
Have a great day!

**Update**:
Unfortunately, I had to stop the bot because Tinkoff blocked it. This wasn’t entirely unexpected — you shouldn’t rely on unofficial APIs.
During its short period of operation, the bot sent 82 notifications and interacted with over 100 users! I hope it managed to help some of you.
