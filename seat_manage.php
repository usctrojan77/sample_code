<?php
include_once 'seat.php';
$rows = 3;
$cols = 11;
$reserved = array("R1C4","R1C6","R2C3","R2C7","R3C9","R3C10");
$n = 5;
$seat_manage = new Seat();

$map = $seat_manage->build($rows, $cols, $reserved);

$map = $seat_manage->reserve($map, $n);

$n = 11;
$map = $seat_manage->reserve($map, $n);

$n = 5;
$map = $seat_manage->reserve($map, $n);

$n = 4;
$map = $seat_manage->reserve($map, $n);
?>