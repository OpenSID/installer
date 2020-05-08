<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="form-group has-feedback">
	<p style='text-align:justify;'>Anda telah berhasil melewati bagian menghubungkan aplikasi ke basisdata. OpenSID sudah dapat berkomunikasi dengan database.</p>
	<p style='text-align:justify'>Untuk memastikan OpenSID dapat berjalan dengan lancar, OpenSID akan melakukan pengecekan requirement server dahulu</p>

</div>
<hr>
<div class="form-group">
	<dl>
		<dt>Sistem Operasi</dt>
		<dd class="label label-info"><?= $server['OS'] ?></dd>
		<dt>Web Server</dt>
		<dd class="label label-info"><?= $server['webServer'] ?></dd>
		<dt>Letak file php.ini</dt>
		<dd class="label label-info"><?= $server['php_ini'] ?></dd>
	</dl>
</div>
<div class="form-group">
	<h3 class="bg-info text-center">Versi</h3>
	<div class="table-responsive">
		<table class="table no-margin">
			<thead>
				<th>Requirements</th>
				<th>Minimal</th>
				<th>Server</th>
				<th>Status</th>
			</thead>
			<tbody>
				<?php
				$hasError = [];
				foreach ($server['versi'] as $key => $value):
				?>
				<tr>
					<td><?= is_array($value['self'] ? $key. "(". $value['self']['distribusi'] .")" : $key ) ?></td>
					<td>> <?= $value['requirement'] ?></td>
					<td><?= is_array($value['self']) ? $value['self']['versi'] : $value['self'] ?></td>
					<?php if(!$value['err']): ?>
					<td> <span class='label label-success'>OK</span></td>
					<?php else: ?>
					<td> <span class='label label-danger'>FAIL</span></td>
					<?php
						$hasError[$key] = [
							"message" => 'Silahkan install' . strtoupper($key) . 'dengan versi yang lebih tinggi dari' . $value['requirement']
						];
						endif
					?>
				</tr>
				<?php
				endforeach
				?>
			</tbody>
		</table>
	</div>
</div>
<div class="form-group">
	<h3 class="bg-info text-center">Config</h3>
	<div class="table-responsive">
		<table class="table no-margin">
			<thead>
				<th>Requirements</th>
				<th>Minimal</th>
				<th>Server</th>
				<th>Status</th>
			</thead>
			<tbody>
				<?php
				foreach ($server['config'] as $key => $value):
				?>
				<tr>
					<td><?= $key ?></td>
					<td><?= $value['requirement'] ?></td>
					<?php if(is_array($value['self'])): ?>
					<td>
						<?php foreach ($value['self'] as $k => $mode):?>
						<?= $mode ?><br>
						<?php endforeach?>
					</td>
					<?php else: ?>
					<td><?= $value['self']?></td>
					<?php endif ?>
					<?php if(!$value['err']): ?>
					<td> <span class='label label-success'>OK</span></td>
					<?php else: ?>
					<td> <span class='label label-danger'>FAIL</span></td>
					<?php
						$hasError[$key] = [
							"message" => 'Silahkan ubah ' . $key . ' di file php.ini'
						];
						endif
					?>
				</tr>
				<?php
				endforeach
				?>
			</tbody>
		</table>
	</div>
</div>
<div class="form-group">
	<h3 class="bg-info text-center">Extensions</h3>
	<div class="table-responsive">
		<table class="table no-margin">
			<thead>
				<th>Requirements</th>
				<th>Minimal</th>
				<th>Server</th>
				<th>Status</th>
			</thead>
			<tbody>
				<?php
				foreach ($server['config'] as $key => $value):
				?>
				<tr>
					<td><?= $key ?></td>
					<td>Loaded</td>
					<td><?= $value['self'] ? 'Loaded' : 'Not Loaded' ?></td>
					<?php if(!$value['err']): ?>
					<td> <span class='label label-success'>OK</span></td>
					<?php else: ?>
					<td> <span class='label label-danger'>FAIL</span></td>
					<?php
						$hasError[$key] = [
							"message" => 'Silahkan load extensions' . $key . ' (Windows dengan mengubah file php.ini, Linux dengan menginstall extensi tersebut)'
						];
						endif
					?>
				</tr>
				<?php
				endforeach
				?>
			</tbody>
		</table>
	</div>
</div>
<?php if (count($hasError) != 0): ?>
	<div class="form-group">
		<h3 class="bg-success text-center">Solusi</h3>
		<div class="table-responsive">
			<table class="table no-margin">
				<thead>
					<th>Error</th>
					<th>Solusi</th>
				</thead>
				<tbody>
					<?php
					foreach ($hasError as $key => $value):
					?>
						<tr>
						<td><?= $key ?></td>
						<td><?= $value['message'] ?></td>
						</tr>";
					<?php
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
<?php endif ?>