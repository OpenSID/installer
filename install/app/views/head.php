<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= ucwords($judul)?> | Instalasi OpenSID</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- Favicon -->
	<link rel="shortcut icon" href="<?= base_url()?>favicon.ico" />
	<!-- Bootstrap 3.3.7 -->
	<link rel="stylesheet" href="<?= base_url()?>assets/bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?= base_url()?>assets/bootstrap/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?= base_url()?>assets/bootstrap/css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?= base_url()?>assets/css/AdminLTE.min.css">
	</head>
		<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-box-body">
			<center>
				<img src="<?=base_url('assets/files/logo/opensid_logo.png');?>" width="30%" alt="Instalasi OpenSID" class="img-responsive"/>
				<br><b>OpenSID <?=VERSION?></b>
				<hr>
				<b><?= strtoupper($judul); ?></b><br><br>
			</center>
			<form method="post">
