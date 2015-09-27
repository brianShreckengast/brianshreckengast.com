<html>
<head>
	<style>

		.scoreCard {

			float:left;
			margin-left:10px;
		}
		.round {
			float:left;
			width:100%;
		}
		.round p {

			clear:both;
		}
		.red {

			color:red;
		}

	</style>
</head>
<body>
	<h1>A Pretty Cool Card Game</h1>
	<form action ="card_game.php" method ="POST">
		<p>Enter the players' names, seperated by columns:</p>
		<input type = "text" name ="playerNames" value ="Enter Names" width= 100>
		<p>How many cards should I deal each round?</p>
		<input type = "text" name ="numberCards" width= 100>
		<br>
		<input type ="submit" name ="submit" value ="Play!">
	</form>

<?php

//Functions

/**
*Return an array representing a deck of cards
 * e.g. array(
 *0 => 2&diams;
 *1 => 3&diams;
 *...
 *12 => King$diams;
 *);
 *@return array
 */
function getDeck()
{	
	//empty array to hold our deck
	$deck = array();
	//array of each suite we can loop through
	$suites = ['&diams;', '&hearts;', '&spades;', '&clubs;'];
	//array of special cards
	$royals = ['Jack', 'Queen', 'King', 'Ace'];
	//loop through each suite
	foreach ($suites as $suite) {

		//Add number cards to deck
		for ($i = 2; $i < 11; $i++){

			if($suite == '&diams;' || $suite == '&hearts;'){

				$deck[] = "<span class = 'red'>".$i.$suite."</span>";
			} else {

				$deck[] = $i.$suite;
			}
			

		}
		//Add royals to deck
		foreach ($royals as $royal){

			$deck[] = $royal.$suite;
		}


	}

return $deck;

}
/**
*Shuffles deck by rearranging array values
 * @param array $deck Full deck of cards (passed by ref)
 *
 * @return void
 */

function shuffleDeck(&$deck) {

	shuffle($deck);
}

function deal(&$players, $numCards, &$shuffledDeck){
	//Loop through players to deal them cards
	foreach ($players as $player => $vals){

		for($i = 1; $i <= $numCards; $i++){
			//Pop cards off of deck and add to player's hand array
			$players[$player]['hand'][] = array_pop($shuffledDeck);

		}
	}


}
/**Returns score value of a single card
*
*@param string representing a card
*@return int value of card
*/
function evalCard($card){

	//Remove HTML characters
	$suites = ['&diams;', '&hearts;', '&spades;', '&clubs;',"<span class = 'red'>", "</span>"];
	$card = str_replace($suites, "", $card);
	//Check if card is integer
	if (intval($card)){
		//If it's an integer, return that int value
		return intval($card);
		
		} else {
			//It's not an int so must be royal, look up point value and return
			switch($card){

				case 'Ace':
					return 1;
					break;
				case 'Jack':
					return 11;
					break;
				case 'Queen':
					return 12;
					break;
				case 'King':
					return 13;
					break;
			}
		}


}
/**
*Returns score value of a hand array and returns the score as int
 * @param array $hand of cards
 *
 * @return $score int
 */
function scoreHand($hand){

	//Initialize score variable to increment on
	$score = 0;
	//Loop through cards in hand
	foreach($hand as $card){

		//Evaluate card's points and add to score variable
		$score += evalCard($card);

		
	}

	return $score;
}
/**
*Prints the cards and scores for a player for a single round
 * @param array $playersHands multidimensional assoc array containing players, their hands, and their score
 *@param string $player the name of the player to print
 *@param int $roundScore the score the player got that round
 *@param int $totalScore the player's total score for the game
 * @return void
 */

function displayHand ($playersHands, $player, $roundScore, $totalScore){

	echo "<div class = 'scoreCard'>";
	echo $player."'s Hand";

	
	echo "<table><tr><th>Card</th><th>Points</th></tr>";
	//Loop through each player's hand
	foreach($playersHands[$player]['hand'] as $card){
		//Print card and score of card to screen
		echo '<tr><td>'.$card.'</td><td>'.evalCard($card)."</td></tr>";
	}
	//Print score of round and total score for player to screen
	echo "<tr><td>Points this Round:</td></td><td>$roundScore</td></tr>";
	echo "<tr><td>Total Points</td></td><td>$totalScore</td></tr>";
	echo "</table></div>";
}
/**
*Plays a single round/hand by shuffling deck, dealing cards, calculating score, and printing to screen
 * @param array $players an array containing the players and their scores (passed by ref)
 *@param int $numCards the number of cards to deal this round
 *@param array $deck the array of cards passed by ref
 *@param int $roundNum the number of the round in the game
 * @return void 
 */

function gameRound(&$players, $numCards, &$deck, $roundNum){
	//shuffle the deck
	shuffleDeck($deck);
	//deal cards out to players
	deal($players, $numCards, $deck);
	//echo new round title to screen
	echo "<div class = 'Round'>";
	echo "<h3>Round Number $roundNum </h3>";
	//loop through players
	foreach($players as $player => $hand){
		//return score of player's hand
		$roundScore = scoreHand($players[$player]['hand']);
		//add hand score to total score
		$players[$player]['score']+= $roundScore;
		//Print hand and score to screen
		displayHand($players,$player,$roundScore,$players[$player]['score']);
		//Clear hand for next round
		$players[$player]['hand'] = [];

	}
	//Print count of remaining cards in deck to screen
	echo "<br><p>Remaining cards: ".count($deck)."</p><br></div>";

}
/**
*Loops through player's scores to find the highest score
 * @param array $players assoc array containing players and their score
 *
 * @return $winner string naming the winner
 */

function findWinner($players){
	//Set max to 0
	$max = 0;
	//Loop through list of players
	foreach($players as $player => $val){
		//access player's score
		$playerScore = $players[$player]['score'];
		
		//If player's score is bigger than max, set player's score to max and player to winner
		if ($playerScore > $max) {

			$max = $players[$player]['score'];

			$winner = $player;
		} 
	}

	return $winner;

	
}
/**
*Controls card game
 * @param array $players list of 
 *
 * @return void
 */

function playCardGame($players, $num_cards){

	//Create assoc. array for players and their scores w/ empty array for their hands
	$playersList = [];
	foreach($players as $player){

		$playersList[$player] = array('score' => 0, 'hand'=> array());


	}
	//create a deck
	$new_deck = getDeck();

	//Initiate round counter
	$round = 1;
	//Play rounds while there are enough cards in deck to do so
	while ((count($players)*$num_cards)<=count($new_deck)){

		gameRound($playersList, $num_cards, $new_deck, $round);

		$round++;

	}
	//Figure out who won and print to screen
	$winner = findWinner($playersList);
	echo "<h3>The Winner is: ".$winner."</h3>";

	
}


//Play the Game

//Make sure these was a submission
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	//Make sure more than one player was entered as well as a card count as int
	if(substr_count($_POST['playerNames'],",")){

		if (intval($_POST['numberCards'])){

			//Turn submission string into an array
			$players = explode(",",$_POST['playerNames']);
			//convert numberCards to an int
			$numberCards = (integer) $_POST['numberCards'];
			
			//Make sure we have enough cards to play one round
			if ((count($players)*$numberCards) <= 52){

				//Play the game!
				playCardGame($players, $numberCards);
			} else {

				echo "We don't have enough cards to play that game.";
			}	

		} else {
			//Player did not enter a number of cards
			echo "Please enter a number of cards";
		}

	} else {
		//Player did not add at least two comma-seperated characters
		echo "Please Enter More Players";
	}
}



?>

</body>
</html>