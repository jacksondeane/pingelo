<div class="row">
	<div class="page-header">
		<h1>Results<small> New Result</small></h1>

	</div>
	<div class="span8">
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
			
			<div style="display: none" class="alert alert-info">INFO</div>

			<div>
				<form method="post" name="add_result" action="<?= Paraglide::url('results', 'add') ?>">
				<table>
					<tr>
						<td id="winner_container" style="width:150px; text-align:left; font-size:18px" ><strong>-</strong></td>
						<input type="hidden" id="post_winner_id" name="post_winner_id" value="" />
						<td style="width:100px; text-align:center">def.</td>
						<td id="loser_container" style="width:150px; text-align:left; font-size:18px"><strong>-</strong></td>
						<input type="hidden" id="post_loser_id" name="post_loser_id" value="" />
						<td>
							<button id="add_result_submit" class="btn btn-primary btn-large" name="add_result_submit" type="submit" value=""><i class="icon-plus icon-white"></i>Add Result</button>
						</td>
					</tr>
				</table>
				</form>
			</div>
			<br />
			<br />
			<div style="float:left; width:400px;">
				<div style="width:150px; text-align:left; float:left">
					<?php foreach ($users as $u => $user): ?>
						<div id='winner_div' value='<?= $user->twitter_username ?>' ><a href="#"><?= $user->twitter_username ?></a></div>
					<?php endforeach; ?>
				</div>
				<div style=" width:100px;text-align:center; float:left">v.</div>
				<div style=" width:150px;text-align:left; float:left">
					<?php foreach ($users as $u => $user): ?>
						<div id="loser_div" value="<?= $user->twitter_username ?>" ><a href="#"><?= $user->twitter_username ?></a></div>
					<?php endforeach; ?>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
	
	<div class="span4">
		<div class="well">
			<form method="post" name="add_player" action="<?= Paraglide::url('results', 'add_player') ?>">
				<label for="">Add a Player: (twitter handle)</label><input type="text" name="new_player_twitter_username" />
				<button id="add_player_submit" class="btn btn-primary btn-large" name="add_player_submit" type="submit" value=""><i class="icon-plus icon-white"></i>Add Player</button>
			</form>
		</div>
	</div>
</div>