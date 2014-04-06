<?php

	define( 'ROOT', dirname( __FILE__ ) .'/' );
	define( 'CONF_INI', getenv( 'CONF_INI' ) ? getenv( 'CONF_INI' ) : "default" );

	$path = ROOT."core/".CONF_INI.".conf.ini";
	$config = CONF_INI;

	$ini = parse_ini_file($path,true);
	if( isset( $_POST[ 'install' ]) && $_POST[ 'install' ] == 'Submit'){
		$conf = array();
		foreach($ini as $key => $value){
			if(is_array($value)){
				$sub_conf = array();
				foreach($value as $subkey => $subvalue){
					$sub_conf[$subkey] = $_POST[$subkey];
				}
				
				$conf[$key] = $sub_conf;
			}
			else{
				$conf[$key] = $_POST[$key];
			}
		}
		
		$content = "";
		foreach ($conf as $key=>$elem) { 
			$content .= "[".$key."]\n"; 
			foreach ($elem as $key2=>$elem2) { 
				$content .= $key2." = \"".$elem2."\"\n"; 
			}
			$content .= "\n";
		}

		if(isset($_POST['delete']))
			rename(ROOT."/install.php", ROOT."/install.php.done");

		$handle = fopen($path, 'w');
		fwrite($handle, $content);
		fclose($handle);
		//echo $_POST['delete'];

		echo "<h2>Settings saved Successfully</h2>";
		exit;
		
	}
			
?>

<form action="" method="post">
	<p><span>CONFIG NAME = <?php echo $config; ?></span></p>

<?php 
	foreach( $ini as $key => $value ){
		if( is_array( $value ) ){
			foreach( $value as $sub_key => $sub_value ){
				echo '<p><span>'.$sub_key.' = <input type="text" name="'.$sub_key.'" value="'.$sub_value.'" /></span></p>';
			}
		}
	} 
?>

	<p><input type="checkbox" name="delete"/><span>Delete Installer</span></p>
	<p><input type="submit" name="install" value="Submit"/></p>
</form>
		