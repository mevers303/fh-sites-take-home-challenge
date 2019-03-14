<?php
// Mark Evers
// 3/13/2019
// FareHarbor PHP Take Home Challenge

namespace PokerHand;


// class for parsing a card string into a suit and face
class Card
{

    protected $_suit;
    protected $_face;

    public function __construct($card_str)
    {

        // are they giving us invalid data?
        if (strlen($card_str) < 2 || strlen($card_str) > 3)
        {
            throw new \UnexpectedValueException("The length of the card string was unexpected: {$card_str}");
        }

        // extract the suit from the very last character of the string
        $suit = strtolower($card_str[-1]);
        // now let's sanity check
        if (preg_match("/[cshd]/", $suit))
        {
            // no reason to be concerned
            $this->_suit = $suit;
        }
        else
        {
            throw new \UnexpectedValueException("Unknown suit: {$suit} <{$card_str}>");
        }

        // extract the face by removing the last character (the suit) from the stringt
        $face = strtoupper(substr($card_str, 0, -1));
        // now let's sanity check
        if (preg_match("/[2-9]|10|[JQKA]/", $face))
        {
            // no reason to be concerned
            $this->_face = $face;
        }
        else
        {
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
