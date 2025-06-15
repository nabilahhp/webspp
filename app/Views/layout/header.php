 <?php 
use App\Models\Nav_model;
use App\Models\Konfigurasi_model;
use App\Libraries\Website;
$this->website          = new Website(); 
$m_menu                 = new Nav_model();
$m_site                 = new Konfigurasi_model();
$site_setting           = $m_site->listing();
$nav_profil             = $m_menu->profil('Profil');
$nav_profil2            = $m_menu->profil('Profil');
$nav_berita             = $m_menu->berita();
$nav_layanan            = $m_menu->profil('Layanan');
$nav_layanan2           = $m_menu->profil('Layanan');
$nav_portfolio          = $m_menu->portfolio();
$nav_prestasi           = $m_menu->prestasi();
$nav_ekstrakurikuler    = $m_menu->ekstrakurikuler();
$nav_fasilitas          = $m_menu->fasilitas();
$nav_link_website       = $m_menu->link_website('Publish');
$nav_download           = $m_menu->download();
$nav_menu               = $m_menu->menu();

$menu_tambahan          = '';
foreach($nav_menu as $nav_menu) {
  $sub_menu             = $m_menu->sub_menu($nav_menu->id_menu);
  if($sub_menu) {
    $sub_menu_tambahan = '';
    foreach($sub_menu as $sub_menu) {
      $sub_menu_tambahan .= '<li><a class="dropdown-item" href="'.$sub_menu->link.'">'.$sub_menu->nama_sub_menu.'</a></li>';
    }
    $menu_tambahan        .= '<li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">'.$nav_menu->nama_menu.'</a>
                  <ul class="dropdown-menu">'.$sub_menu_tambahan.' </ul>
                </li>';
  }else{
    $menu_tambahan        .= '<li class="nav-item">
                  <a class="nav-link" href="'.$nav_menu->link.'">'.$nav_menu->nama_menu.'</a>
                </li>';
  }
}
// echo $menu_tambahan;
?>
 <div class="content-wrapper">
    <!-- /header -->