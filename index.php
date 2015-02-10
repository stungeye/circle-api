<?php

function draw($grid, $max_x, $max_y, $html_mode) {
    $str = ($html_mode) ? '<pre>' : '';
    for ($y = 0; $y < $max_y; $y++) {
        for ($x = 0; $x < $max_x; $x++) {
            $str .= $grid[$y][$x];
        }
        $str .= ($html_mode) ? "<br/>" : "\n";
    }
    return ($html_mode) ? $str . '</pre>' : $str;
}

function render_circle($max_x, $max_y, $radius, $thickness, $antialias) {
    
    $grid = array();
    $shades = $antialias ? array('#','$','@','%','&amp;','0','*',';',':',',','.','~','`') : array('#','#','#','#','#','#','#','#','#','#','#','#','#');
    for ($y = 0; $y < $max_y; $y++) {
        $grid[] = array();
        for ($x = 0; $x < $max_x; $x++) {
            $r = sqrt( pow($x-($radius+$thickness),2) + pow($y-($radius+$thickness),2));
            $delta = abs($radius - $r);
            if ($delta <= $thickness) {
                $pick = (int)($delta/$thickness*10);
                if (($pick > 8) && ($y != 0) && ($grid[$y-1][$x] != ' ')) {
                    $pick += 2;
                }
                $grid[$y][$x] = $shades[$pick];
            } else {
                $grid[$y][$x] = ' ';
            }
            
        }
    }
    return $grid;    
}

function get_with_default($key, $default) {
    if (isset($_GET[$key])) {
        return $_GET[$key];
    } else {
        return $default;
    }
}

$api      = isset($_GET['api']);

if ($api) {
    $html_mode = isset($_GET['html']);
    $radius    = get_with_default('radius', 20);
    $thickness = get_with_default('thickness', 1);
    $antialias = isset($_GET['antialias']);
    $max_x = $max_y = 2 * ($radius + $thickness) + 1;

    $grid = render_circle($max_x, $max_y, $radius, $thickness, $antialias);

    header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 31556926)); 
    echo draw($grid, $max_x, $max_y, $html_mode);
    exit;
} 
    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>ASCII Circle API</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <style type="text/css">
        body{ margin: 2em; }
        pre { line-height: 0.7em; }
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
        var radius = 1;
        var forward = true;
        var cached = new Array();

        function circle() {
            
            if (cached[radius]) {
                $('pre').html(cached[radius]);
                setTimeout(circle, 80);
            } else {
                saved = radius;
                $.get('index.php?api&antialias&thickness=3&radius='+radius, function(data) {
                    $('pre').html(data);
                    cached[saved] = data;
                    setTimeout(circle, 30);
                });
            }

            radius += (forward) ? 1 : -1;

            if (radius > 30) {
                forward = false;
            } else if (radius <= 1) {
                forward = true;
            }
        }
        circle();
    //]]>
    </script>
</head>
<body>
    <h1>ASCII CIRCLE API</h1>
    <p><strong>HTML Mode</strong></p>
    <ul>
       <li>Circle of Radius 15 (Default Thickness of 1): <a href="index.php?api&amp;radius=15&amp;html">?api&amp;radius=15&amp;html</a></li> 
       <li>Circle of Radius 15 and Thickness 4: <a href="index.php?api&amp;radius=15&amp;thickness=4&amp;html">?api&amp;radius=15&amp;thickness=4&amp;html</a></li> 
       <li>Anti-Aliased Circle of Radius 15 and Thickness 4: <a href="index.php?api&amp;radius=15&amp;thickness=4&amp;antialias&amp;html">?api&amp;radius=15&amp;thickness=4&amp;antialias&amp;html</a></li> 
    </ul>
    <p><strong>Text Mode with Newlines (\n)</strong></p>
    <ul>
       <li>Circle of Radius 15 (Default Thickness of 1): <a href="index.php?api&amp;radius=15">?api&amp;radius=15</a></li> 
       <li>Circle of Radius 15 and Thickness 4: <a href="index.php?api&amp;radius=15&amp;thickness=4">?api&amp;radius=15&amp;thickness=4</a></li> 
       <li>Anti-Aliased Circle of Radius 15 and Thickness 4: <a href="index.php?api&amp;radius=15&amp;thickness=4&amp;antialias">?api&amp;radius=15&amp;thickness=4&amp;antialias</a></li> 
    </ul>
    <p><em>Written by <a href="http://stungeye.com">Wally Glutton</a> in response to <a href="http://programthis.net/an-almost-perfect-circle/">a coding challenge</a> on <a href="http://programthis.net">programthis.net</a>.</em></p>
    <h1>Demolition</h1>
    <pre>.</pre>
</body>
</html>
