<?php
function linesIntersect(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3, float $x4, float $y4): bool {
	// Return false if either of the lines have zero length
	//echo "L1 = x1:$x1 y1:$y1 x2: $x2 y2:$y2 L2= x3:$x3 y3:$y3 x4:$x4 y4:$y4<br>\n";
	if ($x1 == $x2 && $y1 == $y2 ||
	    $x3 == $x4 && $y3 == $y4){
	    return false;
	}
	// Fastest method, based on Franklin Antonio's "Faster Line Segment Intersection" topic "in Graphics Gems III" book (http://www.graphicsgems.org/)
	$ax = $x2-$x1;
	$ay = $y2-$y1;
	$bx = $x3-$x4;
	$by = $y3-$y4;
	$cx = $x1-$x3;
	$cy = $y1-$y3;
	$alphaNumerator = $by*$cx - $bx*$cy;
	$commonDenominator = $ay*$bx - $ax*$by;
	if ($commonDenominator > 0){
	    if ($alphaNumerator < 0 || $alphaNumerator > $commonDenominator){
	        return false;
	    }
	} elseif ($commonDenominator < 0){
	    if ($alphaNumerator > 0 || $alphaNumerator < $commonDenominator){
	        return false;
	    }
	}
	$betaNumerator = $ax*$cy - $ay*$cx;
	if ($commonDenominator > 0){
	    if ($betaNumerator < 0 || $betaNumerator > $commonDenominator){
	        return false;
	    }
	} elseif ($commonDenominator < 0){
	    if ($betaNumerator > 0 || $betaNumerator < $commonDenominator){
			return false;
	    }
	}
	 if ($commonDenominator == 0){
	    // This code wasn't in Franklin Antonio's method. It was added by Keith Woodward.
	    // The lines are parallel.
	    // Check if they're collinear.
	    $y3LessY1 = $y3-$y1;
	    $ollinearityTestForP3 = $x1*($y2-$y3) + $x2*($y3LessY1) + $x3*($y1-$y2);   // see http://mathworld.wolfram.com/Collinear.html
	    // If p3 is collinear with p1 and p2 then p4 will also be collinear, since p1-p2 is parallel with p3-p4
	    if ($collinearityTestForP3 == 0){
	        // The lines are collinear. Now check if they overlap.
	        if ($x1 >= $x3 && $x1 <= $x4 || $x1 <= $x3 && $x1 >= $x4 ||
	                  $x2 >= $x3 && $x2 <= $x4 || $x2 <= $x3 && $x2 >= $x4 ||
	                  $x3 >= $x1 && $x3 <= $x2 || $x3 <= $x1 && $x3 >= $x2) {
				if ($y1 >= $y3 && $y1 <= $y4 || $y1 <= $y3 && $y1 >= $y4 ||
	                     $y2 >= $y3 && $y2 <= $y4 || $y2 <= $y3 && $y2 >= $y4 ||
	                     $y3 >= $y1 && $y3 <= $y2 || $y3 <= $y1 && $y3 >= $y2){
					return true;
	            }
	        }
	    }
	    return false;
	}
	return true;
}

function isPointInsidePolly(float $east, float $north, float $xmax, float $ymax, String $geojson): bool {
    $decoded = json_decode($geojson);
	$multiPolygon = $decoded->features[0]->geometry->coordinates;
	$nr_intersections = 0;
	$xout = $xmax + 0.1;  // point outside the region
	$yout = $ymax + 0.1;
    foreach($multiPolygon as $polygon) {
		foreach($polygon as $ring) {
			$xold = -2000000;
			$yold = -2000000;
			foreach($ring as $coord) {
				//echo $coord[1].", ".$coord[0]."<br>\n";
				if ($xold != -2000000) {
					if (linesIntersect($xout, $yout, $east, $north, $xold, $yold, $coord[0], $coord[1])) {
						$nr_intersections++;
						//echo "intersects<br>\n";
					}
				}
				$xold = $coord[0];
				$yold = $coord[1];
			}
		}
	}
    return $nr_intersections%2==1;
}

function isPointInsidePollyandBox(float $east, float $north, float $xmax, float $ymax, float $xmin, float $ymin, String $geojson): bool {
    if ($east<$xmax and $east>$xmin and $north<$ymax and $north>$ymin) {
        return isPointInsidePolly($east, $north, $xmax, $ymax, $geojson);
    } else {
        return false;
    }
}
?>