<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Google Maps JavaScript API Example</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAA1i3A2AVMsUWXBX44vDLeLBRbHvJD7lrrzhX73ZhbfnOuHeGXeRRZOl_Zoq2b4PtNHMy0W3iqC-UF1Q" type="text/javascript"></script>
    <script type="text/javascript">

    function initialize() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map_canvas"));

<? 
        $array = file("coords.dat");

        for ($i = 0; $i < sizeof($array); $i++) {
            $newArr[$i] = split(" ", $array[$i]);
            $allLats[$i] = $newArr[$i][0];
            $allLngs[$i] = str_replace("\n", "", $newArr[$i][1]);
        }


        $meanLat = array_sum($allLats) / sizeof($allLats);
        $meanLng = array_sum($allLngs) / sizeof($allLngs);

        $varLat = 0;
        $varLng = 0;

        for ($i = 0; $i < sizeof($allLats); $i++) {
            $varLat += pow($allLats[$i]-$meanLat, 2);
            $varLng += pow($allLngs[$i]-$meanLng, 2);
        }

        $varLat /= sizeof($allLats);
        $varLng /= sizeof($allLngs);

?>
        map.setCenter(new GLatLng(<? echo $meanLat.",".$meanLng; ?>), 13);
<?

        $minLng = $meanLng - 20*$varLat;
        $maxLng = $meanLng + 20*$varLat;
        $maxLat = $meanLat + 20*$varLat;
        $minLat = $meanLat - 20*$varLat;

        $N = 35;
        for ($i = 0; $i < ($N-1); $i++) {
            for ($j = 0; $j < ($N-1); $j++) {
                $freqArr[$i][$j] = 0;
                $lowerLat[$i] = $minLat +     $i*($maxLat-$minLat)/$N;
                $upperLat[$i] = $minLat + ($i+1)*($maxLat-$minLat)/$N;

                $lowerLng[$j] = $minLng +     $j*($maxLng-$minLng)/$N;
                $upperLng[$j] = $minLng + ($j+1)*($maxLng-$minLng)/$N;
                    }
                }

   $lngSpan = $maxLng - $minLng;
   $latSpan = $maxLat - $minLat;
   $binLngSpan = $lngSpan / $N;
   $binLatSpan = $latSpan / $N;

for ($i = 0; $i < sizeof($allLats); $i++) {
		  $ybin = intval(($allLngs[$i] - $minLng) / $lngSpan * $N);
		  $xbin = intval(($allLats[$i] - $minLat) / $latSpan * $N);
		  if($xbin < 0 or $xbin > $N - 1)
		  continue;
		  if($ybin < 0 or $ybin > $N - 1)
		  continue;
		  $freqArr[$xbin][$ybin]++;
}

        $maxFreq = 0;
        for ($i = 0; $i < ($N-1); $i++) {
	    $maxFreq = max($maxFreq, max($freqArr[$i]));
        }

        for ($i = 0; $i < ($N-1); $i++) {
            for ($j = 0; $j < ($N-1); $j++) {
                $intensity = $freqArr[$i][$j] / $maxFreq;
                if ($intensity <= 0.33) {
                    $colorDecimal = (int)(255 * $intensity / 0.33);
                    $colorHex     = base_convert($colorDecimal, 10, 16);
                    if (strlen($colorHex) < 2) {
                        $colorHex = "0".$colorHex;
                    }
                    $color = "#";
                    $color .= (string)$colorHex;
                    $color .= "0000";
                } else if ($intensity > 0.33 && $intensity <= 0.66 ) {
                    $colorDecimal = (int)(255 * ($intensity-0.33) / 0.33);
                    $colorHex     = base_convert($colorDecimal, 10, 16);
                    if (strlen($colorHex) < 2) {
                        $colorHex = "0".$colorHex;
                    }
                    $color  = "#ff";
                    $color .= (string)$colorHex;
                    $color .= "00";
                } else if ($intensity > 0.66) {
                    $colorDecimal = (int)(255 * ($intensity-0.66) / 0.33);
                    $colorHex     = base_convert($colorDecimal, 10, 16);
                    if (strlen($colorHex) < 2) {
                        $colorHex = "0".$colorHex;
                    }
                    $color  = "#ffff";
                    $color .= (string)$colorHex;
                }

?>
                polygon = new GPolygon([
                                new GLatLng(<? echo $lowerLat[$i].",".$lowerLng[$j]; ?>),
                                new GLatLng(<? echo $lowerLat[$i].",".$upperLng[$j]; ?>),
                                new GLatLng(<? echo $upperLat[$i].",".$upperLng[$j]; ?>),
                                new GLatLng(<? echo $upperLat[$i].",".$lowerLng[$j]; ?>),
                                new GLatLng(<? echo $lowerLat[$i].",".$lowerLng[$j]; ?>)],
                                "#f33f00", 5, 0, <? echo '"'.$color.'"'; ?>, 0.8);
                map.addOverlay(polygon);
<?  
            }
        }


/*
        $offset = 0.001;
        for ($i = 0; $i < sizeof($allLats); $i++) {
            $x  = (float)$allLats[$i];
            $xp = (float)$allLats[$i] + $offset;
            $y  = (float)$allLngs[$i];
            $yp = (float)$allLngs[$i] + $offset;


?>
            polygon = new GPolygon([
                            new GLatLng(<? echo $x.",".$y; ?>),
                            new GLatLng(<? echo $x.",".$yp; ?>),
                            new GLatLng(<? echo $xp.",".$yp; ?>),
                            new GLatLng(<? echo $xp.",".$y; ?>),
                            new GLatLng(<? echo $x.",".$y; ?>)],
                            "#f33f00", 5, 0, "#000000", 1.00);
            map.addOverlay(polygon);
<?
        }

*/
?>


        map.setUIToDefault();
      }
    }

    </script>
  </head>
  <body onload="initialize()" onunload="GUnload()">
    <div id="map_canvas" style="width: 95%; height: 95%"></div>

<? echo $minLng." ".$maxLng."<br>"; ?>
<? echo $minLat." ".$maxLat."<br>"; ?>
<? echo $varLat." ".$varLng."<br>"; ?>
<? echo $maxFreq; ?>


  </body>
</html>
