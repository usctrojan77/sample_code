<?php
/*
 * 
 * Class to manage seat booking and assignment
 * 
 * 
 */
Class Seat {
	private $seat_map; //stores seat assignments
	private $center;	//center street for calculating manhattan distance
	private $rows;	//no of rows of seating
	private $cols;	//no of columns of seating
	
	function __construct() {
		$this->seat_map = array();
		$this->center = '';
	}
	
	/*
	 * Build seat structure by initializing. Also reserve seats  based on reserved array
	 * assignment of zero means seat is vacant and assignment of one means seat has been reserved.
	 * Function accepts rows and columns to built the seating array.
	 */
	public function build($rows, $cols, $reserved) {
		$this->rows = $rows;
		$this->cols = $cols;
		for($i=0;$i<$rows;$i++) {
			for($k=0;$k<$cols;$k++) {
				if(!isset($this->seat_map[$i][$k]) && empty($this->seat_map[$i][$k])) {
					$this->seat_map[$i][$k] = 0;
				}
				if(in_array("R".($i+1)."C".($k+1),$reserved)) {
					$this->seat_map[$i][$k] = 1;
				}
			}
		}
		$this->center = (intval($cols/2))-1;
		return $this->seat_map;
	}
	
	/*
	 * This function builds an array consisting of various combinations of seating arrangement
	 * It has two metric one being the total of manhattan distance for all seats in the combination.
	 * Other being the difference between adjacent seats in the combination.
	 * This facilitates having adjacent seats with the least amount of manhattan distance.
	 * Makes sure that the seat assignments are on one row only.
	 * returns the new seat map which has these reservations recorded.
	 * 
	 */
	public function reserve($map,$no_of_seats) {
		$reserved_seats = array();
		for($i=0;$i<$this->rows;$i++) {
			$unfilled_seats = array_keys($map[$i],0);
			$length = count($unfilled_seats);
			if(count($unfilled_seats)>=$no_of_seats) {
				$count = $no_of_seats;
				for($j=0;$j<($length-1);$j++) {
					$seat_count = $no_of_seats;
					$sum = 0; $diff = 0; $seat_no = ""; 
					for($k=$j;$k<$length;$k++) {
						if($seat_count==0) {
							break;
						}
						if(($length-$k)<$no_of_seats) {
							break;
						}
						if(!empty($seat_no)) {
							$seat_no .= ",";
						}
						$seat_no .= "R".($i+1)."C".($unfilled_seats[$k]+1);
						$sum += (abs($this->center-$unfilled_seats[$k])+$i);
						if($k!=$j) {
							$diff += abs($unfilled_seats[$k]-$unfilled_seats[$k-1]);
						} 
						$seat_count--;
					}
					if(!$seat_count) {
						$tmp = array("seat"=>$seat_no,"sum"=>$sum,"diff"=>$diff);
						array_push($reserved_seats,$tmp);
					}
				}
			}
		}
		if(count($reserved_seats)>0) {
			$map = $this->find_optimum_seating($reserved_seats);
		}
		else {
			echo "Not Available\n";
		}
		return $map;
	}
	
	/*
	 * this function is used to find the best possible combination
	 * first criteria being that the seats being adjacent
	 * second criteria been the combination having the least sum of manhattan distance.
	 * calls the build function to record the reserved seats.
	 * return the new updated seat map.
	 * Outputs the start and the end seats of the combination selected.
	 * 
	 */
	private function find_optimum_seating($reserved_seats) {
		$min_sum = 0; $min_diff = 0;
		$index = 0;
		for($i=0;$i<count($reserved_seats);$i++) {
			if(!$min_diff) {
				$min_diff = $reserved_seats[$i]["diff"];
				$min_sum = $reserved_seats[$i]["sum"];
				$index = $i;
			}
			else {
				if($reserved_seats[$i]["diff"]<=$min_diff) {
					$min_diff = $reserved_seats[$i]["diff"];
					if($min_sum>$reserved_seats[$i]["sum"]) {
						$min_sum = $reserved_seats[$i]["sum"];
						$index = $i;
					}
				}
			}
		}
		$seats = explode(",",$reserved_seats[$index]["seat"]);
		if(count($seats)==0) {
			echo "Not Available";
		}
		else {
			echo trim($seats[0]),"\t-\t",trim($seats[count($seats)-1]),"\n";
		}
		$map = $this->build($this->rows, $this->cols, $seats);
		return $map;
	}
}

?>