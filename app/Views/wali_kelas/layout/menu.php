<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-lightblue elevation-4">
  <!-- Brand Logo -->
  <a href="<?php echo base_url('wali_kelas/dasbor') ?>" class="brand-link">
    <img src="<?php echo $this->website->icon() ?>" alt="<?php echo $this->website->namaweb() ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light"><?php echo $this->website->singkatan() ?></span>
  </a>

  <style type="text/css" media="screen">
    nav ul li ul li i {
      color: yellow;
      margin-left: 10px;
    }
  </style>
  <!-- Sidebar -->
  <div class="sidebar">


    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="<?php echo base_url('wali_kelas/dasbor') ?>" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url('wali_kelas/laporan_tagihan') ?>" class="nav-link">
            <i class="nav-icon fas fa-user-lock"></i>
            <p>Laporan Tagihan Telat</p>
          </a>
        </li>
      </ul>
      <br><br><br>
      <br><br><br>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1><?php echo $title ?></h1>
        </div>

      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- Default box -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo $title ?></h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <div class="card-body" style="min-height: 400px;">

              <?php
              $validation = \Config\Services::validation();
              $errors = $validation->getErrors();
              if (!empty($errors)) {
                echo '<span class="text-danger">' . $validation->listErrors() . '</span>';
              }
              ?>

              <?= session()->getFlashdata('error') ?>
              <?= validation_list_errors() ?>

              <?php if (session('msg')) : ?>
                <div class="alert alert-info alert-dismissible">
                  <?= session('msg') ?>
                  <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                </div>
              <?php endif ?>