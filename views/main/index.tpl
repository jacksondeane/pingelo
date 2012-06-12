<div class="row">
	<div class="span8">
		<div class="well">
			<h2>Leaders:</h2>
			<?php $i = 1; ?>
			<?php foreach ($leaders as $leader): ?>
				<div>
					#<?= $i ?>
					<?= $leader->elo_rank ?>
					<?= $leader->twitter_username ?>
					<?php $losses = $leader->num_games - $leader->num_wins; ?>
					(<?= $leader->num_wins ?> - <?= $losses ?>)
				</div>
				<?php $i++; ?>
			<?php endforeach; ?>
			
		</div>
	</div>
	<div class="span4">
		<div class="well">
			<h2>Last 20:</h2>
			<?php foreach ($last_20_results as $result): ?>
				<?php $winner_change = $result->winner_rank_after - $result->winner_rank_before;  ?>
				<?php $loser_change = $result->loser_rank_before - $result->loser_rank_after;  ?>
				<div><?= $result->winner_user->twitter_username ?> (+<?= $winner_change ?>)  def.  <?= $result->loser_user->twitter_username ?> (-<?= $loser_change ?>)</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>