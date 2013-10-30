<?php
require_once 'bst_node.php';

class RbBst{
	private $root = null;
	private $item_count = 0;
	
   /*************************************************************************
    *  Standard BST search
    *************************************************************************/
	public function is_empty(){
		if($this->get_root() == null){
			return true;
		}else{
			return false;
		}
	}
	
    // value associated with the given key; null if no such key
    public function get($key) {
    	return $this->p_get($this->get_root(), $key); 
    }	
    // value associated with the given key in subtree rooted at x; null if no such key
    private function p_get($x, $key) {
        while ($x != null) {
            $cmp = $x->compare_to($key);
            if($cmp < 0){
            	$x = $x->get_left();
            }else if($cmp > 0){
            	$x = $x->get_right();
            }else{
            	return $x->get_value();
            }
        }
        return null;
    }

    // is there a key-value pair with the given key?
    public function contains($key) {
        return ($this->get($key) != null);
    }

    // is there a key-value pair with the given key in the subtree rooted at x?
    private function p_contains($x, $key) {
        return ($this->get($x, $key) != null);
    }
	
	public function get_root(){
		return $this->root;
	}
	
	public function set_root($x){
		$this->root = $x;
	}
	
	public function search($key) {
		$h = $this->root;
		while ($h != null) {
			$cmp = $h->compare_to($key);
			if ($cmp == 0) {
				return $h;
			} else if ($cmp < 0) {
				$h = $h->get_left();
			} else {
				$h = $h->right;
			}
		}
		return null;
	}
	
	public function get_max(){
		$x = $this->root;
		while($x->get_left() != null){
			$x = $x->get_left();
		}
		return $x;
	}
	
	public function get_min(){
		$x = $this->root;
		while($x->get_right() != null){
			$x = $x->get_right();
		}
		return $x;
	}
	
	public function insert($key,$value){
		$this->root = $this->p_insert($this->root,$key,$value);
	}
	
	// delete the key-value pair with the minimum key
	public function delete_min(){
		if ($this->is_empty()) throw new Exception("BST underflow");
	
		// if both children of root are black, set root to red
		if (!$this->is_red($this->get_root()->get_left()) && !$this->is_red($this->get_root()->get_right()))
			$this->root->color = RED;
	
		$this->set_root($this->p_delete_min($this->get_root()));
		if (!$this->is_empty()){
			$root->set_color('BLACK');
		}
	}
	
	// delete the key-value pair with the minimum key rooted at$h
	private function p_delete_min($h) {
		if ($h->get_left() == null){
			return null;
		}
	
		if (!$this->is_red($h->get_left()) && !$this->is_red($h->get_left()->get_left())){
			$h = $this->move_red_left($h);
		}
			
		$h->set_left($this->delete_min($h->get_left()));
		return $this->balance($h);
	}
	
	
	// delete the key-value pair with the maximum key
	public function delete_max() {
		if ($this->is_empty()) throw new Exception("BST underflow");
	
		// if both children of root are black, set root to red
		if (!$this->is_red($this->root->left) && !$this->is_red($this->root->right)){
			$this->get_root()->set_color('RED');
		}
	
		$this->set_root($this->delete_max($this->get_root()));
		if (!$this->is_empty()){
			$this->get_root()->set_color('BLACK');
		}
		
	}
	
	// delete the key-value pair with the maximum key rooted at$h
	private function p_delete_max($h) {
		if ($this->is_red($h->get_left())){
			$h = $this->rotate_right($h);
		}
		if ($h->get_right() == null){
			return null;
		}
		
		if (!$this->is_red($h->get_right()) && !$this->is_red($h->get_right()->get_left())){
			$h = $this->move_red_right($h);
		}
		$h->right = $this->delete_max($h->get_right());
	
		return balance(h);
	}
	
	// delete the key-value pair with the given key
	public function delete($key) {
		if (!$this->contains($key)) {
			print("symbol table does not contain " + $key);
			return;
		}
	
		// if both children of root are black, set root to red
		if (!$this->is_red($this->get_root()->get_left()) && !$this->is_red($this->get_root()->get_right()))
			$this->get_root()->set_color('RED');
	
		$this->root = $this->p_delete($this->get_root(), $key);
		if (!$this->is_empty()){
			$this->get_root()->set_color('BLACK');
		}
	}
	
	// delete the key-value pair with the given key rooted at$h
	private function p_delete($h, $key) {
		if ($h->compare_to($key) < 0)  {
			if (!$this->is_red($h->get_left()) && !$this->is_red($h->get_left()->get_left()))
				$h = $this->move_red_left($h);
			$h->set_left($this->p_delete($h->get_left(), $key));
		}
		else {
			if ($this->is_red($h->get_left())){
				$h = $this->rotate_right($h);
			}
			if ($h->compare_to($key) == 0 && ($h->get_right() == null)){
				return null;
			}
			if (!$this->is_red($h->get_right()) && $h->get_right() != null && !$this->is_red($h->get_right()->get_left())){
				$h = $this->move_red_right($h);
			}
			
			if ($h->compare_to($key) == 0) {
				$x = $this->p_min($h->get_right());
				$h->set_key($x->get_key());
				$h->set_value($x->get_value());
				// $h->val = get($h->right, min($h->right).key);
				// $h->key = min($h->right).key;
				$h->set_right($this->delete_min($h->get_right()));
			}
			else $h->set_right($this->p_delete($h->get_right(), $key));
		}
		return $this->balance($h);
	}
	
	private function p_insert($node,$key,$value){
		//If we reached a null branch create a node and pass it back up
		if($node == null){
			//print("Creating " . $key . "," . $value . "\n");
			$h = new Node($key, $value);
			return $h;
		}

		
		if ($this->is_red($node->get_left()) && $this->is_red($node->get_right())) {
			$this->color_flip($node);
		}
		
		$cmp = $node->compare_to($key);

		if ($cmp == 0) {
			$node->set_value($value);
		} else if ($cmp < 0) {
			$node->set_left($this->p_insert($node->get_left(), $key, $value));
		} else {
			$node->set_right($this->p_insert($node->get_right(), $key, $value));
		}
		
		
		//Handle the color flippity stuff
		if ($this->is_red($node->get_right()) && $this->is_red($node->get_left())) {
			$node = $this->rotate_left($node);
		}
		if ($this->is_red($node->get_left()) && $this->is_red($node->get_left()->get_left())) {
			$node = $this->rotate_right($node);
		}
		$this->item_count++;
		$this->root = $node;
		return $node;
	}
	
	public function is_red($node){
		if($node == null || $node->get_color() != 'RED') return false;
		return true;
	}
	private function rotate_left($h) {
		$x = $h->get_right();
		$h->set_right($x->get_left());
		$x->set_left($h);
		$x->set_color($h->get_color());
		$h->set_color('RED');
		return $x;
	}
	private function rotate_right($h) {
		$x = $h->get_left();
		$h->set_left($x->get_right());
		$x->set_right($h);
		$x->set_color($h->get_color());
		$h->set_color('RED');
		return $x;
	}

	private function color_flip($h) {
		$h->flip_color();
		$h->get_left()->flip_color();
		$h->get_right()->flip_color();
	}
	
	// Assuming that$h is red and both $h.left and $h.left.left
	// are black, make $h.left or one of its children red.
	private function move_red_left($h) {
		$this->color_flip($h);
		if ($this->is_red($h->get_right()->get_left())) {
			$h->set_right($this->rotate_right($h->get_right()));
			$h = $this->rotate_left($h);
		}
		return $h;
	}
	
	// Assuming that $h is red and both $h.right and $h.right.left
	// are black, make $h.right or one of its children red.
	private function move_red_right($h) {
		$this->color_flip($h);
		if ($this->is_red($h->get_left()->get_left())) {
			$h = $this->rotate_right(h);
		}
		return $h;
	}
	
	// restore red-black tree invariant
	private function balance($h) {
		if ($this->is_red($h->get_right())){
			$h = $this->rotate_left($h);
		}
		if ($this->is_red($h->get_left()) && $this->is_red($h->get_left()->get_left())){
			$h = $this->rotate_right($h);
		}
		if ($this->is_red($h->get_left()) && $this->is_red($h->get_right())){
			$this->flip_colors($h);
		}
	
		$h->set_N($this->p_size($h->get_left()) + $this->p_size($h->get_right()) + 1);
		return $h;
	}
	
	
	/*************************************************************************
	 *  Utility functions
	*************************************************************************/
	
	//$height of tree (1-node tree$has$height 0)
	public function height(){
		return $this->p_height($this->get_root()); 
	}
	private function p_height($x) {
		if ($x == null){
			return -1;
		}
		return 1 + Math.max(height($x->get_left()),$this->p_height($x->get_right()));
	}
	
	/*************************************************************************
	 *  Ordered symbol table methods.
	*************************************************************************/
	
	// the smallest key; null if no such key
	public function min() {
		if ($this->is_empty()){
			return null;
		}
		return $this->p_min($this->get_root())->get_key();
	}
	
	// the smallest key in subtree rooted at x; null if no such key
	private function p_min($x) {
		if ($x->get_left() == null){
			return $x;
		}else{
			return $this->p_min($x->get_left());
		}
	}
	
	// the largest key; null if no such key
	public function max() {
		if ($this->is_empty()){
			return null;
		}
		return $this->p_max($this->get_root())->get_key();
	}
	
	// the largest key in the subtree rooted at x; null if no such key
	private function p_max($x) {
		if ($x->get_right() == null){
			return $x;
		}else{
			return $this->p_max($x->get_right());
		}
	}
	
	// the largest key less than or equal to the given key
	public function floor($key) {
		$x = $this->p_floor($this->get_root(), $key);
		if($x == null){
			return null;
		}
		else{
			return $x->get_key();
		}
	}
	
	// the largest key in the subtree rooted at x less than or equal to the given key
	private function p_floor($x, $key) {
		if ($x == null){
			return null;
		}
		$cmp = $x->compare_to($key);
		if ($cmp == 0){
			return $x;
		}
		if ($cmp < 0){
			return $this->p_floor($x->get_left(), $key);
		}
		$t = $this->p_floor($x->get_right(), $key);
		if($t != null){
			return $t;
		}else{
			return $x;
		}
	}
	
	// the smallest key greater than or equal to the given key
	public function ceiling($key) {
		$x = $this->p_ceiling($this->get_root(), $key);
		if ($x == null){
			return null;
		}else{
			return $x->get_key();
		}
	}
	
	// the smallest key in the subtree rooted at x greater than or equal to the given key
	private function p_ceiling($x, $key) {
		if ($x == null){
			return null;
		}
		$cmp = $x->compare_to($key);
		if ($cmp == 0){
			return $x;
		}
		if ($cmp > 0){
			return $this->p_ceiling($x->get_right(), $key);
		}
		$t = $this->p_ceiling($x->get_left(), $key);
		if ($t != null){
			return $t;
		}else{
			return $x;
		}
	}
	
	
	// the key of rank k
	public function select($k) {
		if ($k < 0 || $k >= $this->get_root->size()){
			return null;
		}
		$x = $this->p_select($this->get_root(), $k);
		return $x->get_key();
	}
	
	// the key of rank k in the subtree rooted at x
	private function p_select($x, $k) {
		$t = $this->p_size($x->get_left());
		if($t > $k){
			return $this->p_select($x->get_left(),  $k);
		}else if($t < $k){
			return $this->p_select($x->get_right(), $k-$t-1);
		}else{
			return $x;
		}
	}
	
	// number of keys less than key
	public function rank($key) {
		return $this->p_rank($key, $this->get_root());
	}
	
	// number of keys less than key in the subtree rooted at x
	private function p_rank($key, $x) {
		if ($x == null){
			return 0;
		}
		$cmp = $x->compare_to($key);
		if($cmp < 0){
			return $this->p_rank($key, $x->get_left());
		}else if($cmp > 0){
			return 1 + $this->p_size($x->get_left()) + $this->p_rank($key, $x->get_right());
		}else{
			return $this->p_size($x->get_left());
		}
	}
	
	/***********************************************************************
	 *  Range count and range search.
	***********************************************************************/
	
	// all of the keys, as an Iterable
	/*
	public Iterable<Key> keys() {
		return keys(min(), max());
	}
	*/
	
	// the keys between lo and$hi, as an Iterable
	/*
	public Iterable<Key> keys(Key lo, Key$hi) {
		Queue<Key> queue = new Queue<Key>();
		// if (isEmpty() || lo.compare_to(hi) > 0) return queue;
		keys(root, queue, lo,$hi);
		return queue;
	}
	*/
	
	// add the keys between lo and$hi in the subtree rooted at x
	// to the queue
	/*
	private function keys($x, $queue, $lo, $hi) {
		if ($x == null) return;
		$cmplo = $x->compare_to($lo);
		$cmphi = $x->compare_to($hi);
		if ($cmplo < 0) $this->keys($x->get_left(), $queue, $lo, $hi);
		if ($cmplo <= 0 && $cmphi >= 0){
			$queue->enqueue($x->gwt_key());
		}
		if ($cmphi > 0){
			$this->keys($x->get_right(), $queue, $lo, $hi);
		}
	}
	*/
	
	// number keys between lo and hi
	public function size($lo, $hi) {
		if ($lo > $hi){
			return 0;
		}
		if ($this->contains($hi)){
			return $this->rank($hi) - $this->rank($lo) + 1;
		}else{
			return $this->rank($hi) - $this->rank($lo);
		}
	}
	
	// number of node in subtree rooted at x; 0 if x is null
	private function p_size($x) {
		if ($x == null) return 0;
		return $x->get_N();
	}
	
	/*************************************************************************
	 *  Check integrity of red-black BST data structure
	*************************************************************************/
	/*
	private function  check() {
		if (!isBST())            StdOut.println("Not in symmetric order");
		if (!isSizeConsistent()) StdOut.println("Subtree counts not consistent");
		if (!isRankConsistent()) StdOut.println("Ranks not consistent");
		if (!is23())             StdOut.println("Not a 2-3 tree");
		if (!isBalanced())       StdOut.println("Not balanced");
		return isBST() && isSizeConsistent() && isRankConsistent() && is23() && isBalanced();
	}
	*/
	
	// does this binary tree satisfy symmetric order?
	// Note: this test also ensures that data structure is a binary tree since order is strict
	private function is_BST() {
		return $this->p_is_BST($this->get_root(), null, null);
	}
	
	// is the tree rooted at x a BST with all keys strictly between min and max
	// (if min or max is null, treat as empty constraint)
	// Credit: Bob Dondero's elegant solution
	private function p_is_BST($x, $min, $max) {
		if ($x == null) return true;
		if ($min != null && $x->compare_to($min) <= 0) return false;
		if ($max != null && $x->compare_to($max) >= 0) return false;
		return $this->p_is_BST($x->get_left(), $min, $x->get_key()) && $this->p_is_BST($x->get_right(), $x->get_key(), $max);
	}
	
	// are the size fields correct?
	public function is_size_consistent() { 
		return $this->p_is_size_consistent($this->get_root()); 
	}
	
	private function p_is_size_consistent($x) {
		if ($x == null){
			return true;
		}
		if ($x->get_N() != $this->p_size($x->get_left()) + $this->p_size($x->get_right()) + 1){
			return false;
		}
		return $this->p_is_size_consistent($x->get_left()) && $this->p_is_size_consistent($x->get_right());
	}

	// check that ranks are consistent
	/*
	private function is_rank_consistent() {
		for (int i = 0; i < size(); i++)
		if (i != rank(select(i))) return false;
		for (Key key : keys())
		if (key.compare_to($this->select($this->rank($key))) != 0){
			return false;
		}
		return true;
	}
	*/

	// Does the tree$have no red right links, and at most one (left)
	// red links in a row on any path?
	public function is23() { 
		return $this->p_is_23($this->get_root()); 
	}
	
	private function p_is_23($x) {
		if ($x == null){
			return true;
		}
		if ($this->is_red($x->get_right())){
			return false;
		}
		if ($x != $this->get_root() && $this->is_red($x) && $this->is_red($x->get_left())){
			return false;
		}
		return $this->p_is_23($x->get_left()) && $this->p_is_23($x->get_right());
	}

	// do all paths from root to leaf$have same number of black edges?
	private function is_balanced() {
	$black = 0;     // number of black links on path from root to min
	$x = $this->get_root();
	while ($x != null) {
		if (!$this->is_red($x)){
			$black++;
		}
		$x = $x->get_left();
	}
	return $this->p_is_balanced($root, black);
	}

	// does every path from the root to a leaf have the given number of black links?
	private function p__is_balanced($x, $black) {
		if ($x == null){
			return $black == 0;
		}
		if (!$this->is_red($x)){
			$black--;
		}
		return $this->p_is_balanced($x->get_left(), $black) && $this->is_balanced($x->get_right(), $black);
	}
}