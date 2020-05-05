<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
				<div class="form-group has-feedback">
					<p style='text-align:justify;'>Anda telah berhasil melewati bagian menghubungkan aplikasi ke basisdata. OpenSID sudah dapat berkomunikasi dengan database.</p>
					<p style='text-align:justify'>Untuk memastikan OpenSID dapat berjalan dengan lancar, OpenSID akan melakukan pengecekan requirement server dahulu</p>
                    
				</div>
				<hr>
				<div class="form-group">
					<dl>
						<dt>Sistem Operasi</dt>
						<dd class="label label-info"><?php echo $server['OS']?></dd>
						<dt>Web Server</dt>
						<dd class="label label-info"><?php echo $server['webServer']?></dd>
						<dt>Letak file php.ini</dt>
						<dd class="label label-info"><?php echo $server['php_ini']?></dd>
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
									foreach ($server['versi'] as $key => $value) {
										echo "<tr>";
										if(!is_array($value['self'])){
											echo "<td>{$key}</td>";
										}else{
											echo "<td>".$key." (".$value['self']['distribusi'].")</td>";
										}
										echo "<td> > {$value['requirement']}</td>";
										if(!is_array($value['self'])){
											echo "<td>{$value['self']}</td>";
										}else{
											echo "<td>{$value['self']['versi']}</td>";
										}
										if(!$value['err']){
											echo "<td> <span class='label label-success'>OK</span></td>";
										}else{
											$hasError[$key] = [
												"message" => 'Silahkan install'.strtoupper($key).'dengan versi yang lebih tinggi dari'.$value['requirement']
											];
											echo "<td> <span class='label label-danger'>FAIL</span></td>";
										}
										echo "</tr>";
									}
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
									foreach ($server['config'] as $key => $value) {
										echo "<tr>";
										echo "<td>{$key}</td>";
										echo "<td>{$value['requirement']}</td>";
										if(!is_array($value['self'])){
											echo "<td>{$value['self']}</td>";
										}else{
											echo "<td>";
											foreach ($value['self'] as $k => $v) {
												echo $v."<br>";
											}
											echo "</td>";
										}
										if(!$value['err']){
											echo "<td> <span class='label label-success'>OK</span></td>";
										}else{
											$hasError[$key] = [
												"message" => 'Silahkan ubah '.$key.' di file php.ini'
											];
											echo "<td> <span class='label label-danger'>FAIL</span></td>";
										}
										echo "</tr>";
									}
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
									foreach ($server['extensions'] as $key => $value) {
										echo "<tr>";
										echo "<td>{$key}</td>";
										echo "<td>Loaded</td>";
										if($value['self']){
											echo "<td>Loaded</td>";
										}else{
											echo "<td>Not Loaded</td>";
										}
										if(!$value['err']){
											echo "<td> <span class='label label-success'>OK</span></td>";
										}else{
											$hasError[$key] = [
												"message" => 'Silahkan load extensions'.$key.' (Windows dengan mengubah file php.ini, Linux dengan menginstall extensi tersebut)'
											];
											echo "<td> <span class='label label-danger'>FAIL</span></td>";
										}
										echo "</tr>";
									}
								?>		
							</tbody>
						</table>
					</div>
				</div>
				<?php if(count($hasError) != 0 ) {?>
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
									foreach ($hasError as $key => $value) {
										echo "<tr>";
										echo "<td>{$key}</td>";
										echo "<td>{$value['message']}</td>";
										echo "</tr>";
									}
								?>		
							</tbody>
						</table>
					</div>
				</div>
				<?php }?>