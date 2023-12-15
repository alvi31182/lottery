# lottery

This is one of the services that involves a lottery draw when a player places a bet. For example, a player registers in the system, makes a deposit, then selects a game and places a sports bet.
Data for the Lottery model is sourced from another service, such as a gaming service. In the gaming service, a user places a bet on a game, and as soon as the bet is made, the gaming service sends events of the player's actions to the Kafka broker. The lottery service subscribes to the **`"player.v1.staked"`** topic with a message type of **`"player.stakeCreated"`**. After the lottery service receives the message data, it creates a lottery for the respective player based on their ID and the ID of the game in which they placed the bet.
