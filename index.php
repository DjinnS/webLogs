<?php

// PARAMETERS
$logs= array(
    1 => "../../../logs/lhttpd-access.log",
    2 => "../../../logs/lhttpd-error.log",
);

// FUNCTIONS

function tailfile($file, $lines) {
    $handle = fopen($file, "r");
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if(fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos --;
        }
        $linecounter --;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines-$linecounter-1] = fgets($handle);
        if ($beginning) break;
    }
    fclose ($handle);
    return array_reverse($text);
}
?>
<html>
<head>
	<title>WebLogs</title>

	<script>
	<!--
		function toBottom(){
	
			nDiv = document.getElementById('log');
			setTimeout("nDiv.scrollTop = nDiv.scrollHeight",1);
		}

		onload=toBottom;
	-->	
	</script>
</head>
<body>

<div style="text-align: center">
	<form method="POST">

		Logfile: 
		<select name="logs">
	        	<option value="1" <?php if(isSet($_POST['logs']) && $_POST['logs'] == 1) echo "selected"; ?>>Access log</option>
		        <option value="2" <?php if(isSet($_POST['logs']) && $_POST['logs'] == 2) echo "selected"; ?>>Error log</option>
		        <option value="3" <?php if(isSet($_POST['logs']) && $_POST['logs'] == 3) echo "selected"; ?>>PHP log</option>
		</select>

		- Lines: <input type="text" name="lines" size="3" value="50" /> (max 1024)

		<input type="submit" value="View"/>
	</form>
</div>

<?php
	if(isSet($_POST['logs'])) {

		if(isSet($logs[$_POST['logs']])) {

			$logfile=$logs[$_POST['logs']];

			$lines=(isSet($_POST['lines']) && $_POST['lines'] <= 1024) ? $_POST['lines'] : 1024;

			if(file_exists($logfile)) {

				$fsize = round(filesize($logfile)/1024/1024,2);

				echo '<div style="text-align: center"><strong>Last '.$lines.' lines of the file '.$logfile.' (size: '.$fsize.' Mb):</strong></div><br />';
	
				$lines = tailfile($logfile, $lines);

				echo '<div style="height: 80%; overflow: auto; background-color: #F0F0F0; border: 1px solid #000000" id="log">';
				foreach ($lines as $line) {
				    echo $line."<br />";
				}
				echo "</div>";
			} else {
				echo "File doesn't exist !";
			}
		}
	}
?>

<hr />

<div style="text-align: center"><a href="https://github.com/DjinnS/WebLogs">webLogs - v 0.1</a></div>
</body>
</html>
