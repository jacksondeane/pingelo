<?php if (!empty($pagination)): ?>
	<div class="pagination">
		<?php if (empty($hide_pagination_count)): ?>
			<div class="pagination-description">Showing <?= $pagination['range'] ?> of <?= $pagination['count'] ?></div>
		<?php endif; ?>
		<?php if ($pagination['pages'] > 1): ?>
			<div class="pagination-links">
				<?= HtmlTable::pagination_links(array(
					'pagination' => $pagination
				)) ?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
