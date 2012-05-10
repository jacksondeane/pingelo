<div class="row">
	<div class="span8">
		<div class="well">
			<?php $i = 1; ?>
			<?php foreach ($leaders as $leader): ?>
				<div>
					#<?= $i ?>
					<?= $leader->elo_rank ?>
					<?= $leader->twitter_username ?>
					(<?= $leader->num_games ?> - <?= $leader->num_wins ?>)
				</div>
				<?php $i++; ?>
			<?php endforeach; ?>
			<br />
			<br />
			<br />
			<?= $new_ratings['a'] ?><br />
			<?= $new_ratings['b'] ?>
		</div>
	</div>
	<div class="span4">
		<div class="well">
		</div>
	</div>
</div>