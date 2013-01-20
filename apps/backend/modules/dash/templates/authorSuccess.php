<div id="sf_admin_container">
	<h1>Author Dashboard</h1>
	
	<div id="sf_admin_content">
	
		<?php $results = $q->fetchAll('SELECT count( id ) AS total, count( `abstract` ) AS abstract, count( `comment` ) AS comment, count( `thumbnail` ) AS thumbnail, count( `birth_date` ) AS birth_date, count( `death_date` ) AS death_date, count( `wikipedia_url` ) AS wikipedia_url, count( `dbpedia_url` ) AS dbpedia_url, count( `dbpedia_at` ) AS dbpedia_at FROM `author`'); ?>
		<div class="sf_admin_list" style="width:500px;">
			<table>
				<thead>
					<tr>
						<th>count</th>
						<th style="width:50px;"></th>
						<th style="width:50px;"></th>
						<th style="min-width:200px;"></th>
					</tr>
				</thead>
				<?php foreach ($results as $key => $result): ?>
					<tr>
						<td>Total</td>
						<td><?php echo $result['total'] ?></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>Query</td>
						<td><?php echo $result['dbpedia_at'] ?></td>
						<td><?php echo ceil($result['dbpedia_at']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['dbpedia_at']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>Abstract</td>
						<td><?php echo $result['abstract'] ?></td>
						<td><?php echo ceil($result['abstract']/$result['dbpedia_at']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['abstract']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>Comment</td>
						<td><?php echo $result['comment'] ?></td>
						<td><?php echo ceil($result['comment']/$result['dbpedia_at']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['comment']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>Thumbnail</td>
						<td><?php echo $result['thumbnail'] ?></td>
						<td><?php echo ceil($result['thumbnail']/$result['dbpedia_at']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['thumbnail']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>Birth_date</td>
						<td><?php echo $result['birth_date'] ?></td>
						<td><?php echo ceil($result['birth_date']/$result['dbpedia_at']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['birth_date']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>Death_date</td>
						<td><?php echo $result['death_date'] ?></td>
						<td><?php echo ceil($result['death_date']/$result['dbpedia_at']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['death_date']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>Wikipedia url</td>
						<td><?php echo $result['wikipedia_url'] ?></td>
						<td><?php echo ceil($result['wikipedia_url']/$result['dbpedia_at']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['wikipedia_url']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>Dbpedia url</td>
						<td><?php echo $result['dbpedia_url'] ?></td>
						<td><?php echo ceil($result['dbpedia_url']/$result['dbpedia_at']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['dbpedia_url']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
				<?php endforeach;?>
			</table>
		</div>
	
</div>