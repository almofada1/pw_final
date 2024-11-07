<?php include "header.php";?>

<main class="main">

	<!-- Hero Section -->
	<section id="hero" class="hero section dark-background">

	<div class="container mt-5" style="">
    <h2>Database Table</h2>
    <input class="form-control mb-4" id="tableSearch" type="text" placeholder="Search...">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody id="myTable">
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>john@example.com</td>
            </tr>
        </tbody>
    </table>

	<script>
		$(document).ready(function(){
			$("#tableSearch").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#myTable tr").filter(function() {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			});
		});
	</script>

	<svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28 " preserveAspectRatio="none">
		<defs>
		<path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
		</defs>
		<g class="wave1">
		<use xlink:href="#wave-path" x="50" y="3"></use>
		</g>
		<g class="wave2">
		<use xlink:href="#wave-path" x="50" y="0"></use>
		</g>
		<g class="wave3">
		<use xlink:href="#wave-path" x="50" y="9"></use>
		</g>
	</svg>
	
</main>

<?php include "footer.php";?>