<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Calculator</title>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<style type="text/css">
			body { font-family: "Poppins", sans-serif; }
		</style>
	</head>
	<body class="bg-secondary">

		<div class="container">
			<div class="row">
				<div class="col-md-12">



						<div class="container">
							<div class="row">			
								<div class="col-md-3"></div>
								<div class="col-md-6">
									<div class="py-5">

										<div class="card shadow-lg">
											<div class="card-header">
												<div class="d-flex justify-content-between">
													<div class="p-2">
														<h5 style="font-size: 20px;" class="card-title h3 mb-0 py-2 bg-light">RS to BDT Pricing</h5>
													</div>
													<?php if ( 2 + 2 == 5 ) { ?>
													<div class="p-2">
														<a href="./" class="btn btn-warning">Home</a>
													</div>
													<?php } ?>
												</div>
											</div>
											<div class="card-body">

												<form action="" method="post">
													<div class="mb-3">
														<label for="product_category" class="form-label">Select Product:</label><br>
												        <select id="product_category" name="product_category" class="form-control text-bg-light" required>
												            <option value="">Select Product Category</option>
												            <option value="300">Kurti/ 1 Pc</option>
												            <option value="500">Kurti Set 2 Pcs</option>
												            <option value="700">Kurti Set 3 Pcs</option>
												            <option value="1250">Salwar Suit</option>
												            <option value="950">Saree (Up to 700G</option>
												            <option value="1500">Saree (Above 700G)</option>
												            <option value="350">Shoes (Up to 300G)</option>
												            <option value="650">Shoes (Up to 500G)</option>
												            <option value="200">Shoes (Up to 800G)</option>
												            <option value="50">Lingerie</option>
												            <option value="200">Watch/ Wallet/ Sunglasses</option>
												            <option value="80">Make Up (Up to 50G)</option>
												            <option value="150">Skincare (Up to 100G)</option>
												            <option value="450">Skincare (Up to 300G)</option>
												            <option value="700">Skincare (Up to 500G)</option>
												            <option value="400">Perfume (Up to 350G)</option>
												            <option value="450">Clutch/ Bag (Up to 300G)</option>
												            <option value="650">Clutch/ Bag (Up to 500G)</option>
												            <option value="1000">Bag (Up to 800G)</option>
												            <option value="1250">Bag (Up to 1KG)</option>
												        </select>
													</div>

													<div class="mb-3">
														<label for="input_Rs_Price" class="form-label">Input Rs Price:</label>
														<input required type="text" id="input_Rs_Price" name="input_Rs_Price" class="form-control text-bg-light" placeholder="Product Price">
													</div>

													<div class="mb-3">
														<input type="submit" value="Calculate" name="cal2" class="btn btn-danger w-100 py-3">
													</div>
												</form>
												<?php
													// Check if the form is submitted
								        			if ( isset($_POST["cal2"]) && isset($_POST['product_category']) && isset($_POST['input_Rs_Price'])) {
													    // Retrieve values from the form
														$input_product_category = $_POST['product_category'];
														$input_prduct_price = $_POST['input_Rs_Price'];

														if ( in_array( $input_product_category, array( 500, 700, 1250, 1500, 350, 650, 1000, 50, 200, 80, 150, 450 ) ) ) {
														    $product_factor = 0;
														} elseif ( in_array( $input_product_category, array( 950 ) ) ) {
														    $product_factor = 1;
														} elseif ( in_array( $input_product_category, array( 300 ) ) ) {
														    $product_factor = -0.166666;
														}

														$actual_conversion_rate 	= 1.6;

														$retail_price_profit 		= 0.3;
														$reselling_price_profit		= 0.2;
														$wholesale_price_profit 	= 0.12;

														$selling_conversion_rate_retail 	= 2.08;
														$selling_conversion_rate_reselling 	= 1.92;
														$wholesale_price_wholesale 			= 1.79;

														$actual_calculation_retail 		= $selling_conversion_rate_retail * $input_prduct_price + $input_product_category;
														$actual_calculation_resell 		= $selling_conversion_rate_reselling * $input_prduct_price + $input_product_category;
														$actual_calculation_wholesale 	= $wholesale_price_wholesale * $input_prduct_price + $input_product_category;

														$retail_25 		= $actual_calculation_retail + 25;
														$resell_25 		= $actual_calculation_resell + 25;
														$wholesale_25	= $actual_calculation_wholesale + 25;

													    // Apply MROUND equivalent in PHP
														$retail_25_rounded_profit = round($retail_25 / 50) * 50;
														$resell_25_rounded_profit = round($resell_25 / 50) * 50;
														$wholesale_25_rounded_profit = round($wholesale_25 / 50) * 50;

														$retail_resell_diff = $retail_25_rounded_profit - $resell_25_rounded_profit;

														if ( $retail_resell_diff < 300 ) {
															$add_static_amount = 300;
														} else {
															$add_static_amount = 0;
														}

														$min_extra_margin = $add_static_amount * $product_factor;

														$final_retail_price = $retail_25_rounded_profit + ($min_extra_margin);
														$final_retail_price_formatted = number_format($final_retail_price, 2);
												?>
												<table class="table table-striped w-100 border">
													<tr>
														<th colspan="2" class="p-0">
															<h4 class="mb-0 py-3 px-2 bg-warning text-center">This will cost: <br class="d-block d-md-none">BDT <?php echo $final_retail_price_formatted; ?>/=</h4>
														</th>
													</tr>
												</table>
												<?php } ?>

											</div>
										</div>
									</div>
								</div>
								<div class="col-md-3"></div>
							</div>
						</div>

				</div>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

		<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
		<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	</body>
</html>