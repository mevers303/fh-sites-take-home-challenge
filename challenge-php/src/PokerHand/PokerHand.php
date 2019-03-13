<?php

namespace PokerHand;


class WrongNumberOfCardsException extends \Exception {

    protected $_nCards;

    public function __construct($nCards)
    {
        parent::__construct();
        $this->_nCards = $nCards;
    }

    public function errorMessage()
    {
        return "Error on line {$this->getLine()} in {$this->getFile()}: Invalid number of cards: <b>{$this->_nCards}</b>";
    }

}


class Card
{

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
                break;
            default:
                // they gave us something unexpected
                throw new \UnexpectedValueException("Unknown face: {$face} <{$card_str}>");
        }

    }
}


class PokerHand
{
    public function __construct($hand)
    {

        $cards = explode(" ", $hand);

        // let's make sure we're playing real poker here, do some sanity checks...

        // check if we have the right number of cards
        if (count($cards) != 5)
        {
            throw new WrongNumberOfCardsException(count($cards));
        }


    }

    public function getRank()
    {
        // TODO: Implement poker hand ranking
        return 'Royal Flush';
    }
}