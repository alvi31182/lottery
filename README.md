<h1>Lottery Service</h1>

![dia.png](public%2FReadmeImg%2Fdia.png)

<ul>
<li>
<h5>Resource isolation:</h5>
</li>
<li>Each container will run in its own environment, which ensures resource isolation. If one of the teams becomes overloaded, it does not affect the others.</li>
<li>
<h5>
Flexibility of configuration:
</h5>
</li>
<li>Different commands may require different dependencies, settings, or PHP versions. Using separate containers provides customization flexibility for each team.</li>
</ul>

<hr>
<ul>
<li>This is one of the services that involves a lottery draw when a player places a bet.</li>
<li>For example, a player registers in the system, makes a deposit, then selects a game and places a sports bet.</li>
<li>Data for the Lottery model is sourced from another service, such as a gaming service. </li>
<li>In the gaming service, a user places a bet on a game, and as soon as the bet is made, the gaming service sends events of the player's actions to the Kafka broker. </li>
<li>The lottery service subscribes to the <b>"player.v1.staked"</b> topic with a message type of <b>"player.stakeCreated"`</b>. After the lottery service receives the message data, it creates a lottery for the respective player based on their ID and the ID of the game in which they placed the bet.</li>
<li>After a specific number of players who placed the initial bet have been gathered, the lottery drawing begins, and the status is updated from "in_waiting" to "started." </li>
<li>The winner is selected, and once the winner is determined, the status in the lottery table is updated from "started" to "finished," and the winner's status set to "winner." </li>
<li>After determining the winner, the data <b>'lottery_id'</b> and <b>'win_sum'</b> are recorded in the <b>`lottery_award`</b> table, and the status <b>'played_out'</b> is added. Once <b>`LotteryAward`</b> is created, the domain event <b>`AwardCreated`</b> is triggered. The event is sent to a subscriber in the Outbox, and the event data is recorded in the <b>`Outbox`</b> table. </li>
<li>The producer (run by a daemon) sends the data from the Outbox table to Kafka, creating the topic <b>'lottery.v1.award'</b> with the messageType <b>`lotteryAwardCreated`</b>.  </li>
<li>Other services, typically <b>`PlayerService`</b>, read this topic to inform the player that they have won a prize.</li>
</ul>


**Database structure**
<img src="public/ReadmeImg/db-gram.png" alt="image" style="width:500px;height:auto;">

**How the Process Works:***

The [KafkaWorker](src%2FCore%2FWorker%2FKafka%2FKafkaWorker.php) initiates a polling loop in Kafka, adding the consumer_group_id **"lottery_service_consumer_group"** Since there can be many participants in the lottery, and consequently, there can be more than a million messages in the **"player.v1.staked"** topic, it is necessary to store these messages in a SplQueue data structure.

Let's imagine that we have a **Game Service** that sends data to a Kafka topic subscribed to by the Lottery Service. The **Lottery Service** receives data from the topic, processes it, and populates the **"lottery"** table, specifying the status as **"in_waiting"**

In the KafkaWorker run symfony console command **"bin/console app:consume"**, the received message about the player who placed a bet is sent to the **[LotteryCreateHandler](src%2FLottery%2FApplication%2FUseCase%2FLotteryCreateHandler.php)**, where the JSON-formatted message is decoded into an associative array. Then a record with the status **"in_waiting"** is saved in the Lottery table.

After obtaining the list of lottery participants, a command is sent to update the status to **"started"** using the **"[UpdateLotteryToStartCommand](src%2FLottery%2FApplication%2FCommand%2FUpdateLotteryToStartCommand.php)"** In the **"[LotteryUpdateStatusToStartedHandler](src%2FLottery%2FApplication%2FUseCase%2FLotteryUpdateStatusToStartedHandler.php)"** this handler updates the Lottery status and selects the list of participants with the status **"started"**.

After the lottery list has been updated to the status **"started"**, run the command in the class **"ProcessRunDetermineWinner"** **"bin/console app:determine_winner"** to determine the lottery winner, get the list of participants and determine the winner. After we have determined the winner, we pass the lottery_id of the winner in the **"[UpdateLotteryToStartCommand](src%2FLottery%2FApplication%2FCommand%2FUpdateLotteryToStartCommand.php)"** to the **"[LotteryUpdateStatusToStartedHandler](src%2FLottery%2FApplication%2FUseCase%2FLotteryUpdateStatusToStartedHandler.php)"** handler to update the status in **"finished"** except for the **lottery_id** of the winner, since his status is updated in **"winner"**.

Next is an example of using domain events for the **Outbox** from the **LotteryAward** model. As soon as we receive the **[LotteryDeterminedWinner](src%2FLottery%2FApplication%2FEvents%2FEventData%2FLotteryDeterminedWinner.php)** in the **[LotteryAwardCreateHandler](src%2FLottery%2FApplication%2FUseCase%2FLotteryAwardCreateHandler.php)**, we create a **[LotteryAward](src%2FLottery%2FModel%2FLotteryAward.php)** and generate an **[AwardCreated](src%2FLottery%2FModel%2FEvents%2FAwardCreated.php)** event object within the **LotteryAward model**. This event object is handled by a subscriber to this event, **App\Lottery\Shared\**[SharedDomainEvent](src%2FLottery%2FShared%2FSharedDomainEvent.php)**, implementing the **[DomainEventSubscriber](src%2FLottery%2FApplication%2FEvents%2FDomainEvents%2FSubscriber%2FDomainEventSubscriber.php)** interface. In this case, a record is created in **lottery_award**, and upon the **AwardCreated** event, a record is generated in the outbox for further processing, sending to the Kafka topic **"lottery.v1.award"** with the **message => eventType "lotteryAwardCreated"**