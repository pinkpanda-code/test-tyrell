<?php

//pool to store all the playing cards
$pool = [];

$types = ["S","H","D","C"];
$numbers = ["A","2","3","4","5","6","7","8","9","X","J","Q","K"];

foreach ($types as $type){
    foreach ($numbers as $number){
        $pool[] = $type."-".$number;
    }
}

//make sure all cards in a shuffled sequences as output example
shuffle($pool);

//return obj to frontend ajax call
$return_obj = [];

$player_number = $_POST['player_number'] ?? null;

//return error message when the number is nil or less than 0
if(is_null($player_number) || $player_number < 0){
    $return_obj["error"] = "Invalid Input";
    $return_obj["message"] = "Input value does not exist or value is invalid";
    header('HTTP/1.1 400 Bad Request');
    die(json_encode($return_obj));
}

$result = [];
$pool_counter = 0; //pool array index start from 0
$player_number_counter = 1; //counter for player number start from 1

if($player_number > 0){
    do {
        //if the pool still have playing cards
        if($pool_counter < sizeof($pool)){
            $insert = $pool[$pool_counter];
        }else{
            $insert = "NA";  //assumption #1 - to indicate players that did not get any cards
        }

        //concatenating the strings for each row
        if(empty($result[$player_number_counter])){
            $result[$player_number_counter] = "";
        }else{
            $result[$player_number_counter] .= ",";
        }
        $result[$player_number_counter] .= $insert;

        //if all the players received card and there are cards left to distribute
        if($player_number_counter == $player_number && $pool_counter < sizeof($pool)){
            $player_number_counter = 1; //reset to player 1 to continue the distribution
        }else{
            $player_number_counter++;
        }

        $pool_counter++;

    } while ($pool_counter < sizeof($pool) || ($player_number > sizeof($pool) && $player_number_counter <= $player_number));
    //if there are cards left to distribute
    //if there cards finish distributed and still got players haven't received any cards

    $return_obj["data"] = $result;
}else{ //player number = 0 not error
    $return_obj["data"] = ["No players to distribute cards"];  //assumption #2 - to indicate no players to distribute cards
}

echo json_encode($return_obj);


