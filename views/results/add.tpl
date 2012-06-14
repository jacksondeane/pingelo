<div class="row">
	<div class="page-header">
		<h1>Results<small> New Result</small></h1>

	</div>
	<div class="span8">
		<a href="<?= $auth_url ?>" >LOGIN</a>
		
		<div class="well">
			<?php if (!empty($error_msg)): ?>			
			<div class="alert alert-error">ERROR</div>
			<?php endif; ?>

			<?php if (!empty($winning_user) && !empty($losing_user)): ?>
			<div class="alert alert-success">
				<?= $winning_user->twitter_username ?> <?= $new_ratings['a'] ?><br />
				<?= $losing_user->twitter_username ?> <?= $new_ratings['b'] ?><br />
			</div>
			<?php endif; ?>
			
			
			<form method="post" name="add_result" action="<?= Paraglide::url('results', 'add') ?>">
				<div>
					
					<table>
						<tr>
	

						</tr>
						
					</table>
					
				</div>
				
				<div style="float:left; width:400px;">
					<div style="width:150px; text-align:left; float:left; background-color:#bbeebb; margin-right:20px;" class="well">
						<h4 style="text-align:center">Select Winner:</h4>
						<br />
						<span id="winner_container" class="btn btn-success disabled" style="width:120px; text-align:left; font-size:18px" ><strong>-</strong></span>
						<br />
						<br />
						<?php foreach ($users as $u => $user): ?>
							<div id='winner_div' value='<?= $user->twitter_username ?>' ><a href="#"><?= $user->twitter_username ?></a></div>
						<?php endforeach; ?>
					</div>
					


					<div style=" width:150px;text-align:left; float:left; background-color:#efbcba" class="well">
						<h4 style="text-align:center">Select Loser:</h4>
						<br />
						<span id="loser_container" class="btn btn-danger disabled" style="width:120px; text-align:left; font-size:18px"><strong>-</strong></span>
						<br />
						<br />
						<?php foreach ($users as $u => $user): ?>
							<div id="loser_div" value="<?= $user->twitter_username ?>" ><a href="#"><?= $user->twitter_username ?></a></div>
						<?php endforeach; ?>
					</div>
				</div>

				
				<input type="hidden" id="post_loser_id" name="post_loser_id" value="" />
				<input type="hidden" id="post_winner_id" name="post_winner_id" value="" />
			</form>

			<div style="clear:both;"></div>
			<br />
			<hr />
			<label for="">Enter Passcode: </label>
			<div class="input-prepend">
    				<span class="add-on"><i class="icon-lock"></i></span><input class="span2" name="new_player_twitter_username" id="prependedInput" size="16" type="text">
  			</div>
  			<button id="add_result_submit" class="btn btn-primary btn-small" name="add_result_submit" type="submit" value="">Add Result</button>

		</div>
	</div>
	
	<div class="span4">
		<div class="well">

			<?php if (!empty($add_error_msg)): ?>
			<div class="alert alert-error">
				<?= $add_error_msg ?>
			</div>
			<?php endif; ?>

			<?php if (!empty($add_success_msg)): ?>
			<div class="alert alert-success">
				<?= $add_success_msg ?>
			</div>
			<?php endif; ?>

			

			<form method="post" name="add_player" action="<?= Paraglide::url('results', 'add_player') ?>">

				<label for="">Add a Player: (twitter handle)</label>
				<div class="input-prepend">
                	<span class="add-on">@</span><input class="span2" name="new_player_twitter_username" id="prependedInput" size="16" type="text">
              	</div>
              	<label for="">Enter Passcode: </label>
              	<div class="input-prepend">
    				<span class="add-on"><i class="icon-lock"></i></span><input class="span2" name="new_player_twitter_username" id="prependedInput" size="16" type="text">
  				</div>

				<button id="add_player_submit" class="btn btn-primary btn-small" name="add_player_submit" type="submit" value="">Add Player</button>
			</form>
		</div>
	</div>
</div>