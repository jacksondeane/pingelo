<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
		['Year', 'Rank'],
		['0', 100],    	
    	<?php if (!empty($results)): ?>
    		<?php $i = 1; ?>
			<?php foreach ($results as $r): ?>
				['<?= $i ?>',  <?= $r?>],
				<?php $i++; ?>
			<?php endforeach; ?>
		<?php endif; ?>
    ]);

    var options = {
      title: 'Company Performance'
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
</script>

<div class="row">
	<div class="span8">
		<div class="well">
			<?= $user->twitter_username ?>
			<div id="chart_div" style="width: 650px; height: 500px;"></div>
		</div>
	</div>
	<div class="span4">
		<div class="well">
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $r): ?>
					<?= $r?><br />
				<?php endforeach; ?>
			<?php endif; ?>

		</div>
	</div>
</div>