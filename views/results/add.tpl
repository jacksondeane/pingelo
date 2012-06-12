<div class="row">
	<div class="page-header">
		<h1>Results<small> New Result (<?= Paraglide::$controller ?>)</small></h1>

	</div>
	<div class="span8">
		<div class="well">
			<div class="alert alert-error">ERROR</div>
			<div class="alert alert-success">
				<?= $winning_user->twitter_username ?> <?= $new_ratings['a'] ?><br />
				<?= $losing_user->twitter_username ?> <?= $new_ratings['b'] ?><br />
			</div>
			
			<div style="display: none" class="alert alert-info">INFO</div>

			<div>
				<form method="post" name="add_result" action="<?= Paraglide::url('results', 'add') ?>">
				<table>
					<tr>
						<td id="winner_container" style="width:150px; text-align:center; font-size:18px" ><strong>-</strong></td>
						<input type="hidden" id="post_winner_id" name="post_winner_id" value="" />
						<td style="width:100px; text-align:center">def.</td>
						<td id="loser_container" style="width:150px; text-align:center; font-size:18px"><strong>-</strong></td>
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
				<div style="width:150px; text-align:center; float:left">
					<?php foreach ($users as $u => $user): ?>
						<div id='winner_div' value='1' ><a href="#"><?= $user->twitter_username ?></a></div>
					<?php endforeach; ?>
				</div>
				<div style=" width:100px;text-align:cent er; float:left">v.</div>
				<div style=" width:150px;text-align:center; float:left">
					<?php foreach ($users as $u => $user): ?>
						<div id="loser_div" ><a href="#"><?= $user->twitter_username ?></a></div>
					<?php endforeach; ?>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
	
	<div class="span4">
		<div class="well">
			4
		</div>
	</div>
</div>