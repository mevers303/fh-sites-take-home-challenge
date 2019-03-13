<?php

namespace PokerHand;


class WrongNumberOfCardsException extends \Exception {

    protected $_nCards;

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


class Card
{

    protected $_suit;
    protected $_face;

    public function __construct($card_str)
    {

        if (strlen($card_str) < 2 || strlen($card_str) > 3)
        {
            throw new \UnexpectedValueException("The length of the card string was unexpected: {$card_str}");
        }

        $suit = strtolower($card_str[-1]);
        switch ($suit)
        {
            case 'c':
            case 's':
            case 'h':
            case 'd':
                // no reason to be concerned
                $this->suit = $suit;
                break;
            default:
                // they gave us something unexpected
                throw new \UnexpectedValueException("Unknown suit: {$suit} <{$card_str}>");
        }

        $face = strtoupper(substr($card_str, 0, -1));
        switch ($face)
        {
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
            case '10':
            case 'J':
            case 'Q':
            case 'K':
            case 'A':
                // no reason to be concerned
                $this->_face = $face;
                break;
            default:
                // they gave us something unexpected
                throw new \UnexpectedValueException("Unknown face: {$face} <{$card_str}>");
        }

    }

    public function getSuit()
    {
        return $this->_suit;
    }

    public function getFace()
    {
        return $this->_face;
    }
     
}


class PokerHand
{

    protected $_cards;

    public function __construct($hand)
    {

        $cards = explode(" ", $hand);

        // let's make sure we're playing real poker here, do some sanity checks...

        // check if we have the right number of cards
        if (count($cards) != 5)
        {
            throw new WrongNumberOfCardsException(count($cards));
        }

        $this->_cards = array_map(function($x) { return new Card($x); }, $cards);

    }

    public function getRank()
    {
        // TODO: Implement poker hand ranking
        return 'Royal Flush';
    }

    public function getSuits()
    {
        return array_map(function($card) { return $card->getSuit(); }, $this->_cards);
    }

    public function getFaces()
    {
        return array_map(function($card) { return $card->getFace(); }, $this->_cards);
    }

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

    public function checkPairs($num_pairs)
    {

        $faces = $this->getFaces();
        $face_counts = array_count_values($faces);  // this gives us a count of how many times each face appears
        $count_counts = array_count_values($face_counts);  // this gives us a count of pairs, triplets, etc

        $pair_count = $count_counts[2];
        // just a random sanity check, how could they possible more than 2 pairs??
        if ($pair_count > 2)
        {
            throw new \UnexpectedValueException("Somehow you got more than two pairs, you have: {$pair_count}");
        }

        return $pair_count == $num_pairs;
    }

    public function checkThreeOfAKind()
    {

        $faces = $this->getFaces();
        $face_counts = array_count_values($faces);  // this gives us a dictionary of how many times each value appears in the array
        
        return in_array(3, $face_counts);

    }

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

    public function checkFullHouse()
    {
        return $this->checkPairs(1) && $this->checkThreeOfAKind();
    }

}


$hand = new PokerHand('Ah Qs 10c 10d 10s');
if ($hand->checkFullHouse())
    echo "Fuck yeah!";