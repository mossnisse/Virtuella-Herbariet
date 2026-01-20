<?php

/*
 * function for checking if an geographical coordinate in latitude, longitude format is inside an geojson shape.
 * great circle's are not handled, but they should also be drawn wrong, so if segments isn't to long it should be good enough
 * polygons crossing the date line -180/180 longitude will probably also be handled incorrectly.
 */

function isPointInsidePolly(float $east, float $north, float $xmax, float $ymax, string $geojson): bool {
    $decoded = json_decode($geojson);
    if (!$decoded) return false;

    // Robustly find the geometry object
    if (isset($decoded->features[0]->geometry)) {
        $geometry = $decoded->features[0]->geometry;
    } elseif (isset($decoded->geometry)) {
        $geometry = $decoded->geometry;
    } else {
        $geometry = $decoded; // Assume the root is the geometry
    }

    if (!isset($geometry->type) || !isset($geometry->coordinates)) return false;

    // Normalize: Ensure we always have an array of Polygons
    $polygons = ($geometry->type === 'Polygon') ? [$geometry->coordinates] : $geometry->coordinates;
    
    $intersections = 0;
    
    // Safety for the North Pole: Latitude cannot exceed 90
    $yout = min(90.0, $ymax + 0.1); 
    
    // Small epsilon to prevent perfect vertex alignment issues
    $testLon = $east + 0.000000001; 

    foreach($polygons as $polygon) {
        foreach($polygon as $ring) {
            $count = count($ring);
            foreach($ring as $i => $p1) {
                // Get the next point (p2), closing the loop
                $p2 = $ring[($i + 1) % count($ring)];
            
                $lon1 = $p1[0]; $lat1 = $p1[1];
                $lon2 = $p2[0]; $lat2 = $p2[1];
            
                // Check if the point is within the latitude range of the segment
                // Using (lat1 > north) != (lat2 > north) automatically handles:
                // - Horizontal segments (ignored)
                // - Ray passing exactly through a vertex (only counts once)
                if (($lat1 > $north) != ($lat2 > $north)) {
                    // Calculate the longitude where the segment crosses the point's latitude
                    $intersectLon = ($lon2 - $lon1) * ($north - $lat1) / ($lat2 - $lat1) + $lon1;
                    // If the intersection is to the "east" of our point, count it
                    if ($east < $intersectLon) {
                        $intersections++;
                    }
                }
            }
        }
    }
    return $intersections % 2 == 1;
}

function isPointInsidePollyandBox(float $east, float $north, float $xmax, float $ymax, float $xmin, float $ymin, String $geojson): bool {
    if ($east<$xmax and $east>$xmin and $north<$ymax and $north>$ymin) {
        return isPointInsidePolly($east, $north, $xmax, $ymax, $geojson);
    } else {
        return false;
    }
}
?>