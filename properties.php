<?php
include 'create_property/config.php';

$stmt = $conn->prepare("SELECT * FROM properties ORDER BY id DESC");
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Villa Agency - Property Listing</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="assets/css/properties.css">

</head>
<body>

<div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
        <span class="dot"></span>
        <div class="dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>

<?php include 'partials/head.php'; ?>

<div class="page-heading header-text">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <span class="breadcrumb">Akomodácie</span>
                <h3>Akomodácie</h3>
            </div>
        </div>
    </div>
</div>

<div class="section properties">
    <div class="container">
        <ul class="properties-filter">
            <li><a class="is_active" href="#!" data-filter="*">Všetky</a></li>
            <li><a href="#!" data-filter=".villa">Villa</a></li>
            <li><a href="#!" data-filter=".apartment">Apartmán</a></li>
            <li><a href="#!" data-filter=".penthouse">Penthouse</a></li>
        </ul>

        <div class="row properties-box">
            <?php foreach ($properties as $property): ?>
            <div class="col-lg-4 col-md-6 align-self-center mb-30 properties-items <?php echo htmlspecialchars($property['type']); ?>">
                <div class="item">
                    <a href="property-details.php?id=<?php echo $property['id']; ?>">
                        <img src="create_property/uploads/<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                    </a>
                    <span class="category">
                        <?php
                        $types = ['villa' => 'Villa', 'apartment' => 'Apartmán', 'penthouse' => 'Penthouse'];
                        echo $types[$property['type']] ?? 'Villa';
                        ?>
                    </span>
                    <h6>€<?php echo number_format($property['price'], 0, ',', '.'); ?></h6>
                    <h4><a href="property-details.php?id=<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['address']); ?></a></h4>
                    <ul>
                        <li>Spálne: <span><?php echo $property['bedrooms']; ?></span></li>
                        <li>Kúpeľne: <span><?php echo $property['bathrooms']; ?></span></li>
                        <li>Plocha: <span><?php echo $property['area']; ?>m²</span></li>
                        <li>Poschodie: <span><?php echo $property['floor']; ?></span></li>
                        <li>Parkovanie: <span><?php echo $property['parking']; ?></span></li>
                    </ul>
                    <div class="main-button">
                        <a href="property-details.php?id=<?php echo $property['id']; ?>">Zobraziť detail</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($properties)): ?>
            <div class="col-lg-12 text-center">
                <p>Žiadne inzeráty zatiaľ neboli pridané.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/isotope.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/counter.js"></script>
<script src="assets/js/custom.js"></script>

</body>
</html>