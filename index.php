<?php
/*
* webLogs - https://github.com/DjinnS/webLogs
*/

// PARAMETERS
$version="v0.1";

$logs=array(
    '1' => array(
		"name" => "Access log",
		"path" => "../../../logs/lhttpd-access.log"
		),
    '2' => array(
		"name" => "Error log",
		"path" => "../../../logs/lhttpd-access.log"
		),
    '3' => array(
		"name" => "not a log",
		"path" => "test.log"
		),
);

// FUNCTIONS
function tailFile($file, $lines) {
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

if(isSet($_POST['logs'])) {

	if(isSet($logs[$_POST['logs']])) {

		$logPath=$logs[$_POST['logs']]["path"];
	
		$lines=(isSet($_POST['lines']) && $_POST['lines'] <= 1024) ? $_POST['lines'] : 1024;
	
		if(file_exists($logPath)) {

			$fsize = round(filesize($logPath)/1024/1024,2);
		
			$lines = tailFile($logPath,$lines);
		}
	}
}

?>
<html>
	<head>
		<title>WebLogs - <?=$version?></title>

		<script>
		<!--
			function toBottom(){
	
				nDiv = document.getElementById('log');
				setTimeout("nDiv.scrollTop = nDiv.scrollHeight",1);
			}

			onload=toBottom;
		-->	
		</script>

		<style>
		<!--
			a {
				text-decoration: none;
			}
		-->
		</style>
	</head>

	<body>

		<div id="form" style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dotted #000000;">
			<form method="POST">
	
				<div style="float: left; padding-right: 20px;"><strong>:: <a href="https://github.com/DjinnS/WebLogs">webLogs</a> ::</strong></div>

				<div style="float: left; padding-right: 20px;">

					Logfile: 
					<select name="logs">
						<?php
							foreach($logs as $key => $value) {
								$selected = (isSet($_POST['logs']) && $key == $_POST['logs']) ? "selected" : "";
								echo '<option value="'.$key.'" '.$selected.'>'.$value['name'].'</option>';
							}
						?>
					</select>
				</div>

				<div style="float: left; padding-right: 20px;">
						Lines: <input type="text" name="lines" size="3" value="50" /> (max 1024)
				</div>

				<div style="float: left; padding-right: 20px;">
					Auto refresh <input type="checkbox" name"autorefresh" />, frequency: <input type="text" name="lines" size="3" value="50" /> (seconds)
				</div>

				<div style="float: left; padding-right: 20px;">
					<input type="submit" value="View log"/>
				</form>

				<?php
					if($fsize) {
						echo '<strong>Last '.$lines.' lines of the file '.$logPath.' (size: '.$fsize.' Mb):</strong>';	
					}
				?>
			</div>
		</div>

		<div style="clear: both;"></div>

		<div style="height: 80%; overflow: auto; background-color: #F0F0F0; border: 1px solid #000000" id="log">
		<?php
			if($lines) {
				foreach ($lines as $line) {
				    echo $line."<br />";
				}
			} else {
				if(isSet($_POST['logs'])) echo "File doesn't exist !";
				else echo "Select a file !";
			}
		?>
		</div>
	</body>
</html>
