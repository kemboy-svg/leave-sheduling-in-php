<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>
<?php
	if(isset($_POST['apply']))
	{
	$empid=$session_id;
	$fname=$_POST['firstname'];
	$lname=$_POST['lastname'];
	$email=$_POST['email'];
	$leave_type=$_POST['leave_type'];
	$fromdate=date('d-m-Y', strtotime($_POST['date_from']));
	$todate=date('d-m-Y', strtotime($_POST['date_to']));
	$description=$_POST['description'];  
	$status=0;
	$isread=0;
	$leave_days=$_POST['leave_days'];
	$datePosting = date("Y-m-d");

	if($fromdate > $todate)
	{
	    echo "<script>alert('End Date should be greater than Start Date');</script>";
	  }
	elseif($leave_days <= 0)
	{
	    echo "<script>alert('YOU HAVE EXCEEDED YOUR LEAVE LIMIT. LEAVE APPLICATION FAILED');</script>";
	  }

	else {
		
		$DF = date_create($_POST['date_from']);
		$DT = date_create($_POST['date_to']);

		$diff =  date_diff($DF , $DT );
		$num_days = (1 + $diff->format("%a"));

		$sql="INSERT INTO tblleaves(firstname,lastname,email,LeaveType,ToDate,FromDate,Description,Status,IsRead,empid,num_days,PostingDate) VALUES(:leave_type,:fromdate,:todate,:description,:status,:isread,:empid,:num_days,:datePosting)";
		$query = $dbh->prepare($sql);

		$query->bindParam(':firstname',$fname,PDO::PARAM_STR);
		$query->bindParam(':lastname',$lname,PDO::PARAM_STR);
		$query->bindParam(':email',$email,PDO::PARAM_STR);
		$query->bindParam(':leave_type',$leave_type,PDO::PARAM_STR);
		$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
		$query->bindParam(':todate',$todate,PDO::PARAM_STR);
		$query->bindParam(':description',$description,PDO::PARAM_STR);
		$query->bindParam(':status',$status,PDO::PARAM_STR);
		$query->bindParam(':isread',$isread,PDO::PARAM_STR);
		$query->bindParam(':empid',$empid,PDO::PARAM_STR);

		$query->bindParam(':num_days',$num_days,PDO::PARAM_STR);
		$query->bindParam(':datePosting',$datePosting,PDO::PARAM_STR);
		$query->execute();
		$lastInsertId = $dbh->lastInsertId();
		if($lastInsertId)
		{
			echo "<script>alert('Leave Scheduling was was successful.');</script>";
			echo "<script type='text/javascript'> document.location = 'leave_history.php'; </script>";
		}
		else 
		{
			echo "<script>alert('Something went wrong. Please try again');</script>";
		}

	}

}

?>

<body>
	<div class="pre-loader">
		<div class="pre-loader-box">
			<div class="loader-logo"><img src="../vendors/images/dd.jpeg" alt=""></div>
			<div class='loader-progress' id="progress_div">
				<div class='bar' id='bar1'></div>
			</div>
			<div class='percent' id='percent1'>0%</div>
			<div class="loading-text">
				Loading...
			</div>
		</div>
	</div>

	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<div class="mobile-menu-overlay"></div>
	<div class="main-container">
	<div class="pb-20">
		<div class="min-height-200px">
			<div class="page-header">
				<div class="row">
					<div class="col-md-6 col-sm-12">
						<div class="title">
							<h4>Leave Scheduling</h4>
						</div>
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">Schedule for Leave</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div style="margin-left: 50px; margin-right: 50px;" class="pd-20 card-box mb-30">
				<div class="clearfix">
					<div class="pull-left">
						<h4 class="text-blue h4">Schedule Form</h4>
						<p class="mb-20"></p>
					</div>
				</div>
				<div class="wizard-content">
					<form method="post" action="">
						<section>
							
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="form-group">
										<label>First Name</label>
										<input name="firstname" type="text" class="form-control wizard-required" required="true" autocomplete="off">
									</div>
								</div>
								<div class="col-md-6 col-sm-12">
									<div class="form-group">
										<label>Last Name</label>
										<input name="lastname" type="text" class="form-control wizard-required" required="true" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="form-group">
										<label>Email Address</label>
										<input name="email" type="email" class="form-control" required="true" autocomplete="off">
									</div>
								</div>
								<div class="col-md-6 col-sm-12">
									<div class="form-group">
										<label>Available Leave Days</label>
										<input name="leave_days" type="number" class="form-control" required="true" autocomplete="off">
									</div>
								</div>
								
							</div>
							<div class="row">
								<div class="col-md-12 col-sm-12">
									<div class="form-group">
										<label>Leave Type :</label>
										<select name="leave_type" class="custom-select form-control" required="true" autocomplete="off">
										<option value="">Select leave type...</option>
										<?php $sql = "SELECT  LeaveType from tblleavetype";
										$query = $dbh -> prepare($sql);
										$query->execute();
										$results=$query->fetchAll(PDO::FETCH_OBJ);
										$cnt=1;
										if($query->rowCount() > 0)
										{
										foreach($results as $result)
										{   ?>                                            
										<option value="<?php echo htmlentities($result->LeaveType);?>"><?php echo htmlentities($result->LeaveType);?></option>
										<?php }} ?>
										</select>
									</div>
								</div>
						
								</div>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										<div class="form-group">
											<label>Start Leave Date :</label>
											<input name="date_from" type="date" class="form-control" required="true" autocomplete="off">
										</div>
									</div>
									<div class="col-md-6 col-sm-12">
										<div class="form-group">
											<label>End Leave Date :</label>
											<input name="date_to" type="date" class="form-control" required="true" autocomplete="off">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-8 col-sm-12">
										<div class="form-group">
											<label>Reason For Leave :</label>
											<textarea id="textarea1" name="description" class="form-control" required length="150" maxlength="150" required="true" autocomplete="off"></textarea>
										</div>
									</div>
									<div class="col-md-4 col-sm-12">
										<div class="form-group">
											<label style="font-size:16px;"><b></b></label>
											<div class="modal-footer justify-content-center">
												<button class="btn btn-primary" name="apply" id="apply" data-toggle="modal">Schedule for&nbsp;Leave</button>
											</div>
										</div>
									</div>
								</div>
							</section>
						</form>
					</div>
				</div>
				</div>
		<?php include('includes/footer.php'); ?>
	</div>
</div>
<!-- js -->
<?php include('includes/scripts.php')?>
</body>
</html>
