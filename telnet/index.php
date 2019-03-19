<?php

include('LXTelnet.php');
$cmd_opens	= ['$00W01N#1F', '$00W02N#1C', '$00W03N#1D', '$00W04N#1A', '$00W05N#1B', '$00W06N#18', '$00W07N#19', '$00W08N#16', '$00W09N#17', '$00W10N#1F', '$00W11N#1E', '$00W12N#1D', '$00W13N#1C', '$00W14N#1B', '$00W15N#1A'];
$cmd_closes	= ['$00W01F#17', '$00W02F#14', '$00W03F#15', '$00W04F#12', '$00W05F#13', '$00W06F#10', '$00W07F#11', '$00W08F#1E', '$00W09F#1F', '$00W10F#17', '$00W11F#16', '$00W12F#15', '$00W13F#14', '$00W14F#13', '$00W15F#12'];

// init, login
$lx = new LXTelnet();
if($lx->init() === false)
{
	echo 'can\'t connection to server';
	return;
}

if($lx->login() === false)
{
	echo 'can\'t login to server';
	return;
}

// handler command
if(isset($_REQUEST['on']))
{
	$id = $_REQUEST['on'];
	$lx->control($cmd_opens[$id]);
}

if(isset($_REQUEST['off']))
{
	$id = $_REQUEST['off'];
	$lx->control($cmd_closes[$id]);
}

// read data
$status = $lx->status();
$lx->close();

?>

<html>
<head>
	<title>LX Control</title>
</head>
<body>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Status</th>
				<th>Cmd</th>
			</tr>
		</thead>
		<tbody>
			<?php for($i = 0; $i < 15; $i++): ?>
			<tr>
				<td>Light <?= ($i + 1); ?></td>
				<td><?= $status[$i] ?></td>
				<td>
					<a href="?on=<?= $i ?>">On</a>
					<a href="?off=<?= $i ?>">Off</a>
				</td>
			</tr>
			<?php endfor; ?>
		</tbody>
	</table>
</body>
</html>