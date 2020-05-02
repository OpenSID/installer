<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
                <div class="form-group has-feedback">
                    <input type="text" name="db_name" class="form-control" placeholder="Nama basisdata" required value="<?= $_SESSION['db_name'] ?>"/>
                    <span class="glyphicon glyphicon-tasks form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" name="db_user" class="form-control" placeholder="Nama pengguna" required value="<?= $_SESSION['db_user'] ?>"/>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="db_pass" class="form-control" placeholder="Sandi" value="<?= $_SESSION['db_pass'] ?>"/>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" name="db_host" class="form-control" placeholder="Host" required value="<?= $_SESSION['db_host'] ?>"/>
                    <span class="glyphicon glyphicon-cloud form-control-feedback"></span>
                </div>
