<?php
class Zend_Filter_Padding implements Zend_Filter_Interface
{
	protected $_length;
	protected $_direction;
	protected $_character;

	public function __construct($length, $character, $direction = 1){
		$this->setLength($length);
		$this->_character = $character;
		$this->setDirection($direction);
	}

	public function filter ($value){
		return str_pad($value, $this->_length,$this->_character,$this->_direction);
	}

	public function getDirection() {
		return $this->direction;
	}

	public function setDirection($direction) {
		switch ($direction) {
			case 1:
				$this->_direction = STR_PAD_LEFT;
				break;
			case 2:
				$this->_direction = STR_PAD_RIGHT;
				break;
			default:
				$this->_direction = STR_PAD_BOTH;
				break;
		}
	}

	public function setCharacter($character) {
		$validator =  new Zend_Validate_NotEmpty();
		if ($validator->isValid($character)) {
			throw new Exception(reset($validator->getMessages()));
		}
		$this->_character = $character;
	}

	public function setLength($length) {
		$validator = new Zend_Validate_Digits();
		if (!$validator->isValid($length)) {
			throw new Exception(reset($validator->getMessages()));
		}
		$this->_length = $length;
	}
}