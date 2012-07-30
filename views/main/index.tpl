<div class="row">
	<div class="span8">
		<div class="well">
			<h2>Leader Board:<small> (players with less than 10 games not shown)</small></h2>
			<table class="table table-striped table-condensed">
				<tbody>
				<?php if (!empty($leaders)): ?>
					<?php $i = 1; ?>
					<?php foreach ($leaders as $leader): ?>
						<tr>
						   	<td>
						   		#<?= $i ?>
						   	</td>

						   	<td>
						   		<?= $leader->elo_rank ?>
						   	</td>

							<td>
								<a href="#" class="thumbnail" style="width:73px; height:73px" >
		      						<img src="https://api.twitter.com/1/users/profile_image?screen_name=<?= $leader->twitter_username ?>&size=bigger" alt="">
		    					</a>
							</td>
							<td>
								<a href="<?= Paraglide::url('users', null, $leader->twitter_username) ?>" ><?= $leader->twitter_username ?></a>
							<?php $losses = $leader->num_games - $leader->num_wins; ?>
							(<?= $leader->num_wins ?> - <?= $losses ?>)
							</td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="span4">
		<div class="well">
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