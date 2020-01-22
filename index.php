<!DOCTYPE html>
<html>
  <body>
<script src="https://pay.gocardless.com/js/beta"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  <?php 
     
    require 'gocardless/vendor/autoload.php';    //gocardless API
  
  if(isset($_GET['mendateId']) && $_GET['mendateId']!=""){
	 
	   $client = new \GoCardlessPro\Client([
			   'access_token' => 'Pass Token here',
				'environment' => \GoCardlessPro\Environment::SANDBOX
			]);
		  $payment= $client->payments()->create([
					  "params" => ["amount" => '200',
								   "currency" => "GBP",
								   "links" => [
									 "mandate" =>'Pass mendate Id here'
								   ]]
					]);
		// echo "<pre>"; print_r($payment->id); die;
		echo "<strong>Your payment ID is </strong>:".$payment->id;
	    die;
  }

  
	if(isset($_POST['filled'])){
		 //echo "<pre>"; print_r($_POST); die;
			$client = new \GoCardlessPro\Client([
				// We recommend storing your access token in an environment variable for security, but you could include it as a string directly in your code
				'access_token' => 'Pass Token here',
				// Change me to LIVE when you're ready to go live
				'environment' => \GoCardlessPro\Environment::SANDBOX
			]);

			try {
				$customers = $client->customers()->create([
				  "params" => ["email" =>$_POST['email_address'],
							   "given_name" => $_POST['customer']['bank_accounts']['account_holder_name'],
							   "family_name" => $_POST['customer']['bank_accounts']['account_holder_name'],
							   "country_code" => $_POST['customer']['country_code']]
				]); 
			}catch (\GoCardlessPro\Core\Exception\ApiException $e) {
			  // Api request failed / record couldn't be created.
			  echo $e->getMessage();
			} catch (\GoCardlessPro\Core\Exception\MalformedResponseException $e) {
			  // Unexpected non-JSON response
			   echo $e->getMessage();
			} catch (\GoCardlessPro\Core\Exception\ApiConnectionException $e) {
			  // Network error
			   echo $e->getMessage();
			}
			

			try {
				$bank = $client->customerBankAccounts()->create([
				  "params" => ["account_number" => $_POST['customer']['bank_accounts']['account_number'],
							   "branch_code" => $_POST['customer']['bank_accounts']['branch_code'],
							   "account_holder_name" => $_POST['customer']['bank_accounts']['account_holder_name'],
							   "country_code" => $_POST['customer']['country_code'],
							   "links" => ["customer" => $customers->id]]
				]); 
			}catch (\GoCardlessPro\Core\Exception\ApiException $e) {
			  // Api request failed / record couldn't be created.
			  echo $e->getMessage();
			} catch (\GoCardlessPro\Core\Exception\MalformedResponseException $e) {
			  // Unexpected non-JSON response
			   echo $e->getMessage();
			} catch (\GoCardlessPro\Core\Exception\ApiConnectionException $e) {
			  // Network error
			   echo $e->getMessage();
			}
			
			$mandate = $client->mandates()->create([
			  "params" => ["links" => ["customer_bank_account" => $bank->id]]
			]); 
			//echo "<pre>";print_r($mandate); die;
			echo "<strong>Manadate ID: </strong>".$mandate->id; 
			echo "<br>";
			echo "<strong>Customer ID: </strong>".$mandate->links->customer; 
			echo "<br>";
			echo "<strong>Customer Bank Account: </strong>".$mandate->links->customer_bank_account; 
			echo "<br><br>";
			echo "<strong>Click on the below link to complete payment</strong>";
			echo "<br>";
			echo "http://greenearthappeal.org/gocardless/index.php?mendateId=".$mandate->id;
		
			/* $payment= $client->payments()->create([
			  "params" => ["amount" => $am,
						   "currency" => "GBP",
						   "links" => [
							 "mandate" => $mandate->id
						   ]]
			]);
			$subs= $client->subscriptions()->create([
			  "params" => ["amount" => $am,
						   "currency" => "GBP",
						   "name" => $planname,
						   "interval_unit" => "yearly",
						   "links" => ["mandate" => $mandate->id]]
			]);

			$success= $subs->id; */
  }
  ?>
<div class="bs-example">
<div class="container">
<div class="col-md-12 col-sm-12">
<h3 class="text-center">GoCardless Authorization</h3> 
<div class="container center_div">
		<div class="dash_inner_content account_box2">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">GoCardless Authorization </h3>
						</div>
						<div class="panel-body">
						<form id="form" action="" method="post"  onsubmit="onSubmit(event)">
								<div class="input-set form-section" error-message-label-container>
										<div class="input-set__row">
											<input class="input" value="GB" type="hidden" name="customer[country_code]" id="customer_country_code" />
										</div>
										<div error-message-label-container>
										  <div class="form-group">
											<div class="input-set__row js-hide">
											  <div class="input-set__item">
												<label for="customer_bank_accounts_account_holder_name">Account holder name</label>
												<input class="input form-control" ng-gc-init-value="ng-gc-init-value" ng-model="connectCtrl.bankAccount.accountHolderName" type="text" name="customer[bank_accounts][account_holder_name]" id="customer_bank_accounts_account_holder_name" required/>
											  </div>
											</div>
											</div>
											 <div class="form-group">
											<label for="email_address">Email Address:</label>
												<input type="text" class="form-control" id="email_address" name="email_address" required>
										  </div>

										   <input class="input" type="hidden" value="hidden" name="filled">
											<!--Order IBAN input differently depending on local details-->
											  <div class="input-set__row">
												<div ng-class="{ 'js-hide': connectDataCtrl.isShowingIban }">
												  <div class="input-set__row">
												 <div class="form-group">
														<div class="input-set__item input-set__item--start">
														  <div class="bank-detail-input u-relative">
														   <label class="label1" for="customer_bank_accounts_branch_code">Your sort code</label>
															<input placeholder="e.g. 10-20-30" model-view-value="true" ng-gc-init-value="ng-gc-init-value" ng-model="connectCtrl.bankAccount.branchCode" ng-gc-clear-value="connectDataCtrl.isShowingIban" class="form-control input input-set__input input-set__input--bank-details  input-set__input--gb-branch-code input-set__input--gb-branch-code-bacs-only" required="required" type="text" name="customer[bank_accounts][branch_code]" id="customer_bank_accounts_branch_code" />
															<bank-icon class="bank-icon" bank-name="{{ connectCtrl.bankName }}"></bank-icon>
														  </div>
														</div>
												</div>
												<div class="form-group">
													<div class="input-set__item input-set__item--end">
													  <div class="bank-detail-input u-relative">
														<label class="label1" for="customer_bank_accounts_account_number">Your account number</label>
														<input placeholder="e.g. 12345678" ng-gc-init-value="ng-gc-init-value" ng-model="connectCtrl.bankAccount.accountNumber" ng-gc-clear-value="connectDataCtrl.isShowingIban" class="form-control input input-set__input input-set__input--bank-details  input-set__input--gb-account-number input-set__input--gb-account-number-bacs-only" required="required" type="text" name="customer[bank_accounts][account_number]" id="customer_bank_accounts_account_number" />
														
													  </div>
													</div>
												</div>

											</div>
												</div>
											  </div>
										  </div>
									  </div>
									   <!-- Submit button & footer for non-JS version -->
									  <div class="form-section payment-details__continue js-hide u-text-center">
										<button class="btn btn-primary u-margin-Bm" type="submit">
										  Submit
										</button>

									  </div>
									</div>

						<h1 id="error"></h1>
					  </form>
					  </div>
				</div>
			 </div>
       </div>
 </div>
 </div>


  <script>
  function onSubmit(event) {
	 return;
    var form = event.target;
    var publishableAccessToken = 'Pass Token here';

    GoCardless.customerBankAccountTokens.create({
      publishable_access_token: publishableAccessToken,
      customer_bank_account_tokens: {
        account_number: jQuery('#customer_bank_accounts_account_number').val(),
        bank_code: jQuery('#customer_bank_accounts_branch_code').val(),
        branch_code: jQuery('#customer_bank_accounts_branch_code').val(),
       // iban: document.getElementById('iban').value,
        account_holder_name: jQuery('#customer_bank_accounts_account_holder_name').val(),
        country_code: jQuery('#customer_country_code').val()
      }
    }, function(response) {
      if (response.error) {
        document.getElementById('error')
          .textContent = 'Error: ' + response.error.message;
      } else {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.value = response.customer_bank_account_tokens.id;
        input.name = 'customer_bank_account_token';
        form.appendChild(input);

        form.submit();
      }
    });

    // Prevent form submission
    event.preventDefault();
  }
  </script>
  <style>	
.center_div{
    margin: 0 auto;
    width:50%;
}
</style>
  </body>
</html>