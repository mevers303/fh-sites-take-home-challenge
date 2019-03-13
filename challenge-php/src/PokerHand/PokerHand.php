<?php

namespace PokerHand;


class WrongNumberOfCardsException extends Exception {

    protected $_nCards;

    public function __construct($nCards)
    {
        parent::__construct();
        $this->_nCards = $nCards;
    }

    public function errorMessage()
    {
      //error message
      $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
      .': Invalid number of cards: <b>' . $this->_nCards . '</b>';
      return $errorMsg;
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