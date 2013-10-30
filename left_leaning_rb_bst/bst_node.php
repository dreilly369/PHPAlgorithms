<?php

class Node{
	private $color = 'RED';
	private $left = null;
	private $right = null;
	private $key = "";
	private $value = "";
	private $N = 0;
	
	function __construct($key,$val) {
		$this->key = $key;
		$this->value = $val;
		//print("New Node Created: k=>" . $this->key . " v=>" . $this->value . "\n");
	}
	
	function compare_to($key) {
		if($this->key < $key){
			return -1;
		}else if($this->key > $key){
			return 1;
		}else{
			return 0;
		}
	}
	
	public function flip_color(){
		if($this->color == 'RED'){
			$this->color = 'BLACK';
		}else{
			$this->color = 'RED';
		}
	}
	
	// number of node in subtree rooted at x; 0 if x is null
	public function size() {
		return $this->get_N();
	}
	
	//Getters
	public function get_color(){
		return $this->color;
	}
	public function get_value(){
		return $this->value;
	}
	public function get_key(){
		return $this->key;
	}
	public function get_left(){
		return $this->left;
	}
	public function get_right(){
		return $this->right;
	}
	public function get_N(){
		return $this->N;
	}
	//Setters
	public function set_color($c){
		$this->color =$c;
	}
	public function set_value($v){
		$this->value = $v;
	}
	public function set_key($k){
		$this->key = $k;
	}
	public function set_left($node){
		$this->left = $node;
	}
	public function set_right($node){
		$this->right = $node;
	}
	public function set_N($n){
		$this->N = $n;
	}
}