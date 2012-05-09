<?php if (!empty($choices)): ?>
	<table class="styled-table">
		<thead>
			<tr>
				<th>Choice</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($choices as $i => $choice): ?>
				<tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
					<td><?= $choice->name ?></td>
					<td>
						<form class="styled-form" method="post">
							<input type="hidden" name="delete_choice" value="1" />
							<input type="hidden" name="choice_id" value="<?= $choice->id ?>" />
							<input class="button" type="submit" value="Remove" />
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
	There are no choices for this event.
<?php endif; ?>
<br /><br />
<form class="styled-form" method="post">
	<input choice="text" name="name" value="" />
	<input class="button" type="submit" value="Add" />
</form>
