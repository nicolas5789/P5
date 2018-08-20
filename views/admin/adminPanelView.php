<?php ob_start(); ?>
<div id="bloc_haut_admin_panel">
	<h1>Administration</h1>
	<a class="btn btn-secondary" href="#ancreNounous">Gestion des nounous</a>
	<a class="btn btn-secondary"href="#ancreParents">Gestion des parents</a>
	<a class="btn btn-secondary"href="#ancreAvis">Gestion des avis</a>
</div>


<h2 id="ancreNounous">Liste des Nounous</h2>

<table class="table_pag" id="table_nounous">
	<thead>
		<tr>
			<th>Pseudo</th>
			<th class="respDesign">Expérience</th>
			<th class="respDesign">Place(s) disponible(s)</th>
			<th class="respDesign">Ville de résidence</th>
			<th>Nb signalements</th>
			<th>Profil</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($nounous as $nounou): ?>
			<tr>
				<td><?= $nounou->pseudo(); ?></td>
				<td class="respDesign"><?= $nounou->experience(); ?> an(s)</td>
				<td class="respDesign"><?= $nounou->place_dispo(); ?></td>
				<td class="respDesign"><?= $nounou->ville(); ?></td>
				<td><?= $nounou->signalement(); ?></td>
				<td><a class="btn btn-primary" href="index.php?action=adminEditNounou&amp;pseudo=<?= $nounou->pseudo(); ?>">Gérer</a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<hr>

<h2 id="ancreParents">Liste des Parents</h2>

<table class="table_pag" id="table_parents">
	<thead>
		<tr>
			<th>Pseudo</th>
			<th class="respDesign">Nom</th>
			<th class="respDesign">Prénom</th>
			<th>Ville de résidence</th>	
			<th>Gérer</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($parents as $parent): ?>
			<tr>
				<td><?= $parent->pseudo(); ?></td>
				<td class="respDesign"><?= $parent->nom(); ?></td>
				<td class="respDesign"><?= $parent->prenom(); ?></td>
				<td><?= $parent->ville(); ?></td>	
				<td><a class="btn btn-primary" href="index.php?action=adminEditParent&amp;pseudo=<?= $parent->pseudo(); ?>">Gérer</a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	
</table>

<hr>

<h2 id="ancreAvis">Liste des Avis</h2>

<table class="table_pag" id="table_avis">
	<thead>
		<tr>
			<th>Pseudo de l'auteur</th>
			<th>Commentaire</th>
			<th class="respDesign">Note</th>
			<th class="respDesign">Signalement(s)</th>
			<th class="respDesign">Supprimer</th>
			<th>Gérer</th>	
		</tr>
	</thead>
	<tbody>
		<?php foreach ($listAvis as $avis): ?>
			<tr>
				<td><?= $avis->pseudo_parent(); ?></td>
				<td><?= $avis->contenu(); ?></td>
				<td class="respDesign"><?= $avis->note(); ?></td>
				<td class="respDesign"><?= $avis->signalement(); ?></td>
				<td class="respDesign"><a class="btn btn-danger" href="index.php?action=adminPanelDeleteAvis&amp;idAvis=<?= $avis->id(); ?>">Supprimer</a></td>
				<td><a class="btn btn-primary" href="index.php?action=adminShowAvis&amp;idNounou=<?= $avis->id_nounou(); ?>#avis_nounou">Nounou liée</a></td>	
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
		
<?php $content = ob_get_clean(); ?>

<?php require("views/template.php"); ?>