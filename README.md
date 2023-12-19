# lottery Service

![dia.png](public%2FReadmeImg%2Fdia.png)

This is one of the services that involves a lottery draw when a player places a bet. For example, a player registers in the system, makes a deposit, then selects a game and places a sports bet.
Data for the Lottery model is sourced from another service, such as a gaming service. In the gaming service, a user places a bet on a game, and as soon as the bet is made, the gaming service sends events of the player's actions to the Kafka broker. The lottery service subscribes to the **`"player.v1.staked"`** topic with a message type of **`"player.stakeCreated"`**. After the lottery service receives the message data, it creates a lottery for the respective player based on their ID and the ID of the game in which they placed the bet.
After a specific number of players who placed the initial bet have been gathered, the lottery drawing begins, and the status is updated from "in_waiting" to "started." The winner is selected, and once the winner is determined, the status in the lottery table is updated from "started" to "finished," and the winner's status set to "winner."
After determining the winner, the data **`'lottery_id'`** and **`'win_sum'`** are recorded in the **`lottery_award`** table, and the status 'played_out' is added. Once **`LotteryAward`** is created, the domain event **`AwardCreated`** is triggered. The event is sent to a subscriber in the Outbox, and the event data is recorded in the **`Outbox`** table. The producer (run by a daemon) sends the data from the Outbox table to Kafka, creating the topic 'lottery.v1.award' with the messageType **`lotteryAwardCreated`**. 
Other services, typically **`PlayerService`**, read this topic to inform the player that they have won a prize.

**Database structure**
![db-gram.png](public/ReadmeImg/db-gram.png)

**How the Process Works:***

The [KafkaWorker](src%2FCore%2FWorker%2FKafka%2FKafkaWorker.php) initiates a polling loop in Kafka, adding the consumer_group_id **"lottery_service_consumer_group"** Since there can be many participants in the lottery, and consequently, there can be more than a million messages in the **"player.v1.staked"** topic, it is necessary to store these messages in a SplQueue data structure, specifying the desired size for retrieving data from the Kafka topic.

Let's imagine that we have a **Game Service** that sends data to a Kafka topic subscribed to by the Lottery Service. The **Lottery Service** receives data from the topic, processes it, and populates the **"lottery"** table, specifying the status as **"in_waiting"**

The process of a console command is initiated using the Symfony console command **"bin/console app:get_lottery_list"** to retrieve a list of new lotteries with the status **"in_waiting"**

After obtaining the list of lottery participants, a command is sent to update the status to **"started"** using the **"[UpdateLotteryToStartCommand](src%2FLottery%2FApplication%2FCommand%2FUpdateLotteryToStartCommand.php)"** In the **"[LotteryUpdateStatusToStartedHandler.php](src%2FLottery%2FApplication%2FUseCase%2FLotteryUpdateStatusToStartedHandler.php)"** this handler updates the Lottery status and selects the list of participants with the status **"started"**.