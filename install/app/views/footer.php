<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

				<hr>
				<div class="form-group">
					<?php if(@$server['hasError']):?>
						<input type="hidden" name="act" value="cek_server">
						<button type="submit" class="btn btn-primary btn-block btn-flat">Refresh</button>
					<?php else:?>
						<input type="hidden" name="act" value="<?= $tujuan?>">
						<button type="submit" class="btn btn-primary btn-block btn-flat"><?= $aksi?></button>
					<?php endif;?>
				</div>
			</form>
		</div>
	</div>
	<!-- Bootstrap 3.3.7 -->
	<script src="<?= base_url()?>assets/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
