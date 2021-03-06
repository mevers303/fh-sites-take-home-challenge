<?php
// Mark Evers
// 3/13/2019
// FareHarbor PHP Take Home Challenge

namespace PokerHand;

require_once 'Card.php';



// An exception class when they provide the wrong number of cards
class WrongNumberOfCardsException extends \Exception
{

    protected $_nCards;  // how many cards they supplied

    public function __construct($nCards)
    {
        parent::__construct();
        $this->_nCards = $nCards;
    }

    public function __toString()
    {
        return "Error on line {$this->getLine()} in {$this->getFile()}: Invalid number of cards: <b>{$this->_nCards}</b>";
    }

}


// A class for parsing a hand of poker
class PokerHand
{

    protected $_cards;  // an array of Card() objects

    public function __construct($hand)
    {

        $cards = explode(" ", $hand);

        // check if we have the right number of cards
        if (count($cards) != 5)
        {
            throw new WrongNumberOfCardsException(count($cards));
        }

        // convert the strings into objects and save them
        $this->_cards = array_map(function($x) { return new Card($x); }, $cards);

    }

    public function getRank()
    {
        if ($this->checkRoyalFlush())
            return 'Royal Flush';
        if ($this->checkStraightFlush())
            return 'Straight Flush';
        if ($this->checkXOfAKind(4))
            return 'Four of a Kind';
        if ($this->checkFullHouse())
            return 'Full House';
        if ($this->checkFlush())
            return 'Flush';
        if ($this->checkStraight())
            return 'Straight';
        if ($this->checkXOfAKind(3))
            return 'Three of a Kind';
        if ($this->checkPairs(2))
            return 'Two Pair';
        if ($this->checkPairs(1))
            return 'One Pair';
        // they didn't get any hand at all :(
        return 'High Card';
    }

    // returns an array of just the suits of our cards
    public function getSuits()
    {
        return array_map(function($card) { return $card->getSuit(); }, $this->_cards);
    }

    // returns an array of just the faces of our cards
    public function getFaces()
    {
        return array_map(function($card) { return $card->getFace(); }, $this->_cards);
    }

    // returns an array of just the faces of our cards as a numeric value.
    // jack is 11, queen is 12, etc. Ace is 14 and also 1
    public function getFacesAsNumeric()
    {
        $faces = $this->getFaces();

        // if they have an ace, let's add it as a '1' card before we begin
        if (in_array('A', $faces))
        {
            $faces[] = '1';
        }

        // let's create a lambda function that will handle both numeric [0-9] and face [JQKA] together
        $lambda = function($face)
                  {

                      // first, we can take care of the number cards e-z
                      if (is_numeric($face))
                      {
                          return (int)$face;
                      }
                    
                      // now we turn the face cards into a numeric value so they can be compared later
                      switch ($face)
                      {
                          case 'J':
                              return 11;
                          case 'Q':
                              return 12;
                          case 'K':
                              return 13;
                          case 'A':
                              return 14;
                          default:
                              throw new \UnexpectedValueException("We found an unexpected face card while searching for flushes: {$face}");
                      }
                  };
        
        // return new array full of numbers
        return array_map($lambda, $faces);

    }

    // looks for $num_pairs number of pairs.  so 1 is a single pair, 2 is a two-pair
    public function checkPairs($num_pairs)
    {

        $faces = $this->getFaces();
        $face_counts = array_count_values($faces);  // this gives us a count of how many times each face appears
        $count_counts = array_count_values($face_counts);  // this gives us a count of pairs, triplets, etc

        $pair_count = array_key_exists(2, $count_counts) ? $count_counts[2] : 0;
        // just a random sanity check, how could they possible more than 2 pairs??
        if ($pair_count > 2)
        {
            throw new \UnexpectedValueException("Somehow you got more than two pairs, you have: {$pair_count}");
        }

        return $pair_count == $num_pairs;
    }

    // looks for $num_common_cards card matches.
    // 3 is three of a kind, 4 is four of a kind
    public function checkXOfAKind($num_common_cards)
    {

        $faces = $this->getFaces();
        $face_counts = array_count_values($faces);  // this gives us a dictionary of how many times each value appears in the array
        
        return in_array($num_common_cards, $face_counts);

    }

    // returns true for a straight
    public function checkStraight()
    {

        $numeric_faces = $this->getFacesAsNumeric();

        // we're going to loop through each card we have, then see if we can count upwards 4 more cards to get our straight
        foreach ($numeric_faces as $card)
        {

            $good_cards = 1;  // how many cards in the flush so far
            while (TRUE)
            {

                // wrap around ace to 2
                if ($card == 14)
                {
                    $next_card = 2;
                }
                else
                {
                    $next_card = $card + 1;
                }

                // the next card for the flush isn't here, move on
                if (! in_array($next_card, $numeric_faces))
                {
                    break;
                }

                // if we've reached this code, it's a good card
                $good_cards++;
                
                // check if we're at the end of the flush
                if ($good_cards >= 5)
                {
                    return TRUE;
                }

                // we're not, recurse variable for next loop
                $card = $next_card;

            }

        }

        // we didn't find a flush
        return FALSE;

    }

    // returns true for a flush
    public function checkFlush()
    {
        // all we have to do is count the number of unique values in the suits to make sure it's 1
        $suits = $this->getSuits();
        return count(array_unique($suits)) == 1;
    }

    // returns true for a full house
    public function checkFullHouse()
    {
        return $this->checkPairs(1) && $this->checkXOfAKind(3);
    }

    // returns true for a stright flush
    public function checkStraightFlush()
    {
        return $this->checkStraight() && $this->checkFlush();
    }

    // returns true for a royal flush
    public function checkRoyalFlush()
    {

        // check for the straight flush first
        if (! $this->checkStraightFlush())
        {
            return FALSE;
        }

        // check if it has a 10 and an Ace. if it does, we're good
        $faces = $this->getFaces();
        return in_array('10', $faces) && in_array('A', $faces);

    }

}

