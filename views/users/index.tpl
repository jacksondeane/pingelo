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
      title: 'Rank', backgroundColor: 'white'
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
</script>

<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Opponent', 'Win %'],
          ['v. Dan', 80.3],
          ['v. Hadley', 50.5],
          ['v. Oleg',  10.5],
          ['v. Andrew', 20.4],
          ['v. Marc',10.30],
          ['v. Dave',  32.0],
          ['v. Matt',  44.2],
          ['v. Mike',  0.5],
        ]);

        var options = {
          //title: 'Company Performance',
          //vAxis: {title: 'Year',  titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div2'));
        chart.draw(data, options);
      }
</script>


<div class="row">
	<div class="span12">
		<div class="well">
			<h2><?= $user->twitter_username ?></h2>
			<div id="chart_div" style="width: 1000px; height: 600px;"></div>
		</div>
		<div class="well">
			<div id="chart_div2" style="width: 900px; height: 500px;"></div>
		</div>
	</div>
</div>