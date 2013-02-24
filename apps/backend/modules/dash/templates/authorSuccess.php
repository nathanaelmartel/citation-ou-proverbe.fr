<div id="sf_admin_container">
	<h1>Author Dashboard</h1>
	
	<div id="sf_admin_content">
	
		<?php $results = $q->fetchAll('SELECT count( id ) AS total, count( `dbpedia_at` ) AS dbpedia_at, count( `wikipedia_at` ) AS wikipedia_at FROM `author`');  ?>
		<div class="sf_admin_list" style="width:300px;float:left;margin-right:50px;">
			<table>
				<thead>
					<tr>
						<th>count</th>
						<th style="width:50px;"></th>
						<th style="width:50px;"></th>
						<th style="min-width:150px;"></th>
					</tr>
				</thead>
				<?php foreach ($results as $key => $result): $total = $result['total']; ?>
					<tr>
						<td>Total</td>
						<td><?php echo $total ?></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>dbpedia</td>
						<td><?php echo $result['dbpedia_at'] ?></td>
						<td><?php echo ceil($result['dbpedia_at']/$total*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['dbpedia_at']/$total*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>wikipedia</td>
						<td><?php echo $result['wikipedia_at'] ?></td>
						<td><?php echo ceil($result['wikipedia_at']/$total*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['wikipedia_at']/$total*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
				<?php endforeach;?>
				<?php $results = $q->fetchAll('SELECT count( `has_thumbnail` ) AS has_thumbnail FROM `author` WHERE has_thumbnail > 0');  ?>
				<?php foreach ($results as $key => $result):  ?>
					<tr>
						<td>has thumbnail</td>
						<td><?php echo $result['has_thumbnail'] ?></td>
						<td><?php echo ceil($result['has_thumbnail']/$total*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['has_thumbnail']/$total*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
				<?php endforeach;?>
			</table>
		</div>
	
		<?php $results_dbpedia = $q->fetchAll('SELECT count( id ) AS total, count( `name` ) AS name, count( `abstract` ) AS abstract, count( `comment` ) AS comment, count( `thumbnail` ) AS thumbnail, count( `birth_date` ) AS birth_date, count( `death_date` ) AS death_date, count( `wikipedia_url` ) AS wikipedia_url, count( `dbpedia_url` ) AS dbpedia_url FROM `author_dbpedia`'); ?>
		<div class="sf_admin_list" style="width:400px;float:left;margin-right:50px;">
			<table>
				<thead>
					<tr>
						<th>dbpedia</th>
						<th style="width:50px;"></th>
						<th style="width:50px;"></th>
						<th style="min-width:150px;"></th>
					</tr>
				</thead>
				<?php foreach ($results_dbpedia as $key => $result): ?>
					<tr>
						<td>Total</td>
						<td><?php echo $result['total'] ?></td>
						<td><?php echo ceil($result['total']/$total*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['total']/$total*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>name</td>
						<td><?php echo $result['name'] ?></td>
						<td><?php echo ceil($result['name']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['name']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>abstract</td>
						<td><?php echo $result['abstract'] ?></td>
						<td><?php echo ceil($result['abstract']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['abstract']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>comment</td>
						<td><?php echo $result['comment'] ?></td>
						<td><?php echo ceil($result['comment']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['comment']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>thumbnail</td>
						<td><?php echo $result['thumbnail'] ?></td>
						<td><?php echo ceil($result['thumbnail']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['thumbnail']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>birth date</td>
						<td><?php echo $result['birth_date'] ?></td>
						<td><?php echo ceil($result['birth_date']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['birth_date']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>death date</td>
						<td><?php echo $result['death_date'] ?></td>
						<td><?php echo ceil($result['death_date']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['death_date']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>wikipedia url</td>
						<td><?php echo $result['wikipedia_url'] ?></td>
						<td><?php echo ceil($result['wikipedia_url']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['wikipedia_url']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>dbpedia url</td>
						<td><?php echo $result['dbpedia_url'] ?></td>
						<td><?php echo ceil($result['dbpedia_url']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['dbpedia_url']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
				<?php endforeach;?>
			</table>
		</div>
	
		<?php $results_wikipedia = $q->fetchAll('SELECT count( id ) AS total, count( `name` ) AS name, count( `abstract` ) AS abstract, count( `thumbnail` ) AS thumbnail, count( `wikipedia_url` ) AS wikipedia_url FROM `author_wikipedia`'); ?>
		<div class="sf_admin_list" style="width:400px;float:left;margin-right:50px;">
			<table>
				<thead>
					<tr>
						<th>wikipedia</th>
						<th style="width:50px;"></th>
						<th style="width:50px;"></th>
						<th style="min-width:150px;"></th>
					</tr>
				</thead>
				<?php foreach ($results_wikipedia as $key => $result): ?>
					<tr>
						<td>Total</td>
						<td><?php echo $result['total'] ?></td>
						<td><?php echo ceil($result['total']/$total*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['total']/$total*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>name</td>
						<td><?php echo $result['name'] ?></td>
						<td><?php echo ceil($result['name']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['name']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>abstract</td>
						<td><?php echo $result['abstract'] ?></td>
						<td><?php echo ceil($result['abstract']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['abstract']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>thumbnail</td>
						<td><?php echo $result['thumbnail'] ?></td>
						<td><?php echo ceil($result['thumbnail']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['thumbnail']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
					<tr>
						<td>wikipedia url</td>
						<td><?php echo $result['wikipedia_url'] ?></td>
						<td><?php echo ceil($result['wikipedia_url']/$result['total']*100) ?>%</td>
						<td><div style="width:<?php echo ceil($result['wikipedia_url']/$result['total']*100) ?>%;background:#C64934;height:1em;"></div></td>
					</tr>
				<?php endforeach;?>
			</table>
		</div>
		
		<div class="clear"></div>
	
</div>