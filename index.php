<?php session_start(); ?>
<!DOCTYPE html>
<html lang="sk">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>Villa Agency</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="/Airbnb/assets/css/head.css">

    <link rel="icon" href="data:,">
    
  </head>

<body>

<?php include 'partials/head.php'; ?>
<?php if (isset($_SESSION['flash_message'])): ?>
  <div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="z-index: 9999; position: relative;">
      <i class="fa fa-check-circle me-2"></i> <?php echo $_SESSION['flash_message']; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

  <div class="main-banner">
    <div class="owl-carousel owl-banner">
      <div class="item item-1">
        <div class="header-text">
          <span class="category">Nitra, <em>Slovensko</em></span>
          <h2><br>Ubytovanie vašich snov</h2>
        </div>
      </div>
      <div class="item item-2">
        <div class="header-text">
          <span class="category">Nitra, <em>Slovensko</em></span>
          <h2>Luxus<br>aký inde nenájdene</h2>
        </div>
      </div>
      <div class="item item-3">
        <div class="header-text">
          <span class="category">Miami, <em>Južná Florida</em></span>
          <h2><br>ubytovanie, <br> ktoré vyrazí dych</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="featured section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="left-image">
            <img src="assets/images/featured.jpg" alt="">
            <a href="property-details.html"><img src="assets/images/featured-icon.png" alt="" style="max-width: 60px; padding: 0px;"></a>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="section-heading">
            <h6>| Vybrané</h6>
            <h2>Najlepší apartmán s výhľadom na more</h2>
          </div>
          <div class="accordion" id="accordionExample">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Aké sú najužitočnejšie odkazy?
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                Získajte <strong>najlepšiu vilu</strong> šablónu webovej stránky v HTML CSS a Bootstrap pre vašu firmu. TemplateMo vám poskytuje <a href="https://www.google.com/search?q=best+free+css+templates" target="_blank">najlepšie bezplatné CSS šablóny</a> na svete. Prosím, povedzte svojim priateľom o tejto stránke.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Aké ubytovanie ponúkate?
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  Ponúkame exkluzívny výber prémiového ubytovania navrhnutého tak, aby splnilo očakávania aj tých najnáročnejších hostí. Naše portfólio je rozdelené do troch hlavných kategórií, z ktorých každá ponúka jedinečný zážitok, maximálne súkromie a špičkový komfort
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Prečo je Villa Agency najlepšia?
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  Na rozdiel od iných portálov prechádza každá vila a apartmán v našej ponuke prísnou kontrolou kvality a čistoty. Garantujeme vám transparentné ceny bez skrytých poplatkov, okamžitú online rezerváciu a 24/7 zákaznícku podporu.
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="info-table">
            <ul>
              <li>
                <img src="assets/images/info-icon-01.png" alt="" style="max-width: 52px;">
                <h4>250 m2<br><span>Celková plocha bytu</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-02.png" alt="" style="max-width: 52px;">
                <h4>Zmluva<br><span>Zmluva je pripravená</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-03.png" alt="" style="max-width: 52px;">
                <h4>Platba<br><span>Proces platby</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-04.png" alt="" style="max-width: 52px;">
                <h4>Bezpečnosť<br><span>24/7 Pod kontrolou</span></h4>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="video section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Videá</h6>
            <h2>Pozrite si bližšie a pociťujte rozdiel</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="video-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div class="video-frame">
            <img src="assets/images/video-frame.jpg" alt="">
            <a href="https://youtube.com" target="_blank"><i class="fa fa-play"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="fun-facts">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="wrapper">
            <div class="row">
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="34" data-speed="1000"></h2>
                   <p class="count-text ">Nových<br>Destinácií</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="12" data-speed="1000"></h2>
                  <p class="count-text ">Rokov<br>Skúsenosti</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="24" data-speed="1000"></h2>
                  <p class="count-text ">Získaných<br>Ocenení</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section best-deal">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="section-heading">
            <h6>| Top ponuka</h6>
            <h2>Rezervujte si ubytovanie na mieru</h2>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="tabs-content">
            <div class="row">
              <div class="nav-wrapper ">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="appartment-tab" data-bs-toggle="tab" data-bs-target="#appartment" type="button" role="tab" aria-controls="appartment" aria-selected="true">Apartmán</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="villa-tab" data-bs-toggle="tab" data-bs-target="#villa" type="button" role="tab" aria-controls="villa" aria-selected="false">Vila</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="penthouse-tab" data-bs-toggle="tab" data-bs-target="#penthouse" type="button" role="tab" aria-controls="penthouse" aria-selected="false">Penthouse</button>
                  </li>
                </ul>
              </div>              
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="appartment" role="tabpanel" aria-labelledby="appartment-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                          <li>Celková plocha bytu <span>185 m2</span></li>
                          <li>Číslo podlažia <span>26.</span></li>
                          <li>Počet miestností <span>4</span></li>
                          <li>Parkovanie dostupné <span>Áno</span></li>
                          <li>Proces platby <span>Banka</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/apartment1.jpg" alt="apartman" class="detail">
                    </div>
                    <div class="col-lg-3">
                      <h4>Prečo sú naše apartmány tou najlepšou voľbou?</h4>
                      <p>Autentický zážitok z lokality: Naše apartmány sa nachádzajú v tých najlepších štvrtiach – priamo v srdci pulzujúceho mesta alebo len pár krokov od pláže či lyžiarskeho svahu. Otvoríte dvere a ste okamžite v centre diania.Komfort a vybavenie ako doma: Vlastná plne vybavená kuchyňa, kde si môžete kedykoľvek pripraviť ranné espresso alebo lokálne dobroty z trhu. Obývacia izba, kde si večer hodíte nohy na stôl a pozriete si film. Jednoducho maximálne pohodlie.</p>
                      <div class="icon-button">
                        <a href="properties.php"><i class="fa fa-calendar"></i> Dohodnúť si návštevu</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="villa" role="tabpanel" aria-labelledby="villa-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                          <li>Celková plocha bytu <span>250 m2</span></li>
                          <li>Číslo podlažia <span>26.</span></li>
                          <li>Počet miestností <span>5</span></li>
                          <li>Parkovanie dostupné <span>Áno</span></li>
                          <li>Proces platby <span>Banka</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/villa1.jpg" alt="villa" class="detail">
                    </div>
                    <div class="col-lg-3">
                      <h4>Prečo sú naše vily tou najlepšou voľbou pre vašu dovolenku?</h4>
                      <p>Celý pozemok, azúrový bazén, slnečná terasa a dizajnové priestory patria iba vám a vašim najbližším.Zabudnite na stiesnené izby.Naše vily ponúkajú veľkorysé obývacie priestory, plne vybavené moderné kuchyne a kráľovské spálne s výhľadom, ktorý vám vyrazí dych.<br><br></p>
                      <div class="icon-button">
                        <a href="properties.php"><i class="fa fa-calendar"></i> Dohodnúť si návštevu</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="penthouse" role="tabpanel" aria-labelledby="penthouse-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                          <li>Celková plocha bytu <span>320 m2</span></li>
                          <li>Číslo podlažia <span>34.</span></li>
                          <li>Počet miestností <span>6</span></li>
                          <li>Parkovanie dostupné <span>Áno</span></li>
                          <li>Proces platby <span>Banka</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/penthouse1.jpg" alt="penthouse" class="detail" >
                    </div>
                    <div class="col-lg-3">
                      <h4>Ďalšie informácie o Penthause</h4>
                      <p>Zabudnite na kompromisy. Penthouse nie je len ubytovanie – je to vyhlásenie životného štýlu. Tento exkluzívny apartmán na samom vrchole budovy predstavuje absolútny vrchol luxusu, dizajnu a súkromia, aký môžete v meste zažiť.<br><br>Výhľad, ktorý vám vyrazí dych: Celé mesto alebo pobrežie máte ako na dlani priamo z vašej obývačky vďaka panoramatickým oknám od podlahy až po strop.</p>
                      <div class="icon-button">
                        <a href="properties.php"><i class="fa fa-calendar"></i> Dohodnúť si návštevu</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



<?php include 'partials/footer.php'; ?>

  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/counter.js"></script>
  <script src="assets/js/custom.js"></script>

  </body>
</html>