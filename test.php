<?php 

	if($_SERVER["REQUEST_METHOD"] == "POST") {

		try {

			$stream = fopen("php://input", "rb");

			if (!$stream) throw new Exception("cannot open input stream");

			$data = stream_get_contents($stream);

			$ebc = json_decode($data, true);
			
			print_r($ebc);
		}
		catch(Exception $e) {
            print_R($e);
		}
		finally {
			fclose($stream);
		}

	}

 ?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<script type="text/javascript">
			const array = ["one","two","three", 1230, 1458, ["five", "six"] , true, false]
			fetch("<?php echo htmlentities($_SERVER['PHP_SELF']); ?>", {
				method: "POST",
				headers: {
					"content-type": "application/json; charset=utf8",
					"x-requested-with": "xmlhttprequest"
				},
				body: JSON.stringify( array ) 
			})
	</script>
</body>
</html>