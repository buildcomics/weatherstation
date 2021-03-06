<?php 
/* insert.php for Inserting data into database (temporarily) and copying to main database
 * Written by Tom van den Berg for Event-engineers
 */
 ?>
	<div class="container">
			<h1>Upload Data (Max allowed file size: <?php echo ini_get("upload_max_filesize"); ?>).</h1>
			<div id="upload_target"></div>
			<br><br>
			<div id="status" style="padding: 1.4em;"></div>
	</div>
	<script src="js/core.js"></script> <!-- Formstone core -->
	<script src="js/upload.js"></script><!-- Formstone Upload Javascript -->
	<script src="js/insert.php"></script> <!-- Insert Javascript stuff -->

