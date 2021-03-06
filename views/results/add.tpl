<div class="row">
	<div class="page-header">
		<h1>Results<small> New Result</small></h1>
	</div>

	<div class="span12">
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
			<div style="float:left; width:400px;">
				<div style="width:150px; text-align:left; float:left; background-color:#bbeebb; margin-right:20px;" class="well">
					<h4 style="text-align:center">Select Winner:</h4>
					<br />
					<span id="winner_container" class="btn btn-success disabled" style="width:120px; text-align:left; font-size:18px" ><strong>-</strong></span>
					<br />
					<br />
					<?php foreach ($users as $u => $user): ?>

						<div id='winner_div' value='<?= $user->twitter_username ?>' >
							<a href="#"><?= $user->twitter_username ?></a>
						</div>
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
			
			<div style="clear:both;"></div>

			<button id="add_result_submit" class="btn btn-success btn-primary" name="add_result_submit" type="submit" value="">
				<i class="icon-plus-sign icon-white"></i>
				Add Result
			</button>
			</form>
			<hr />

  			<div>

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
	              	
					<button id="add_player_submit" class="btn btn-primary btn-primary" name="add_player_submit" type="submit" value="">
						<i class="icon-user icon-white"></i>
						Add Player
					</button>
				</form>

			</div>
			<hr />
			<div>
				<?php if(!empty($last_20_results)): ?>
			<h2>Last 20:</h2>
			<?php foreach ($last_20_results as $result): ?>
				<?php $winner_change = $result->winner_rank_after - $result->winner_rank_before;  ?>
				<?php $loser_change = $result->loser_rank_before - $result->loser_rank_after;  ?>

				<div>
					<span class="label label-success">+<?= $winner_change ?></span>
					<a href="<?= Paraglide::url('users', null, $result->winner_user->twitter_username) ?>" ><?= $result->winner_user->twitter_username ?></a>
					
					   <em>def.</em>  <a href="<?= Paraglide::url('users', null, $result->loser_user->twitter_username) ?>" ><?= $result->loser_user->twitter_username ?></a> 

				</div>
			<?php endforeach; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
	


</div>