<?php
include 'create_property/config.php';

class PropertyFilter
{
    private array $conditions = [];
    private array $params = [];

    public function applySearch(string $value): void
    {
        if (empty($value)) return;
        $this->conditions[] = "(title LIKE ? OR address LIKE ?)";
        $this->params[] = '%' . $value . '%';
        $this->params[] = '%' . $value . '%';
    }

    public function applyType(string $value): void
    {
        if (empty($value)) return;
        $this->conditions[] = "type = ?";
        $this->params[] = $value;
    }

    public function applyMinPrice(string $value): void
    {
        if (empty($value)) return;
        $this->conditions[] = "price >= ?";
        $this->params[] = $value;
    }

    public function applyMaxPrice(string $value): void
    {
        if (empty($value)) return;
        $this->conditions[] = "price <= ?";
        $this->params[] = $value;
    }

    public function applyBedrooms(string $value): void
    {
        if (empty($value)) return;
        $this->conditions[] = "bedrooms >= ?";
        $this->params[] = $value;
    }

    public function getWhereClause(): string
    {
        if (empty($this->conditions)) return "WHERE 1=1";
        return "WHERE " . implode(" AND ", $this->conditions);
    }

    public function getParams(): array
    {
        return $this->params;
    }
}

class PropertyRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findAll(PropertyFilter $filter): array
    {
        $sql = "SELECT * FROM properties " . $filter->getWhereClause() . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($filter->getParams());
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class PropertyView
{
    public function renderCard(array $property): string
    {
        $types = ['villa' => 'Villa', 'apartment' => 'Apartmán', 'penthouse' => 'Penthouse'];
        $type  = htmlspecialchars($property['type']);
        $id    = $property['id'];
        $label = $types[$property['type']] ?? 'Villa';
        $price = number_format($property['price'], 0, ',', '.');
        $title = htmlspecialchars($property['title']);
        $addr  = htmlspecialchars($property['address']);
        $image = htmlspecialchars($property['image']);

        return <<<HTML
        <div class="col-lg-4 col-md-6 align-self-center mb-30 properties-items {$type}">
            <div class="item">
                <a href="property-details.php?id={$id}">
                    <img src="create_property/uploads/{$image}" alt="{$title}">
                </a>
                <span class="category">{$label}</span>
                <h6>€{$price}</h6>
                <h4><a href="property-details.php?id={$id}">{$addr}</a></h4>
                <ul>
                    <li>Spálne: <span>{$property['bedrooms']}</span></li>
                    <li>Kúpeľne: <span>{$property['bathrooms']}</span></li>
                    <li>Plocha: <span>{$property['area']}m²</span></li>
                    <li>Poschodie: <span>{$property['floor']}</span></li>
                    <li>Parkovanie: <span>{$property['parking']}</span></li>
                </ul>
                <div class="main-button">
                    <a href="property-details.php?id={$id}">Zobraziť detail</a>
                </div>
            </div>
        </div>
        HTML;
    }

    public function renderEmpty(): string
    {
        return '<div class="col-lg-12 text-center"><p>Žiadne ubytovanie nebolo nájdené. <a href="properties.php">Zobraziť všetky</a></p></div>';
    }

    public function selected(string $key, string $value): string
    {
        return ($_GET[$key] ?? '') === $value ? 'selected' : '';
    }

    public function val(string $key): string
    {
        return htmlspecialchars($_GET[$key] ?? '');
    }
}

$filter = new PropertyFilter();
$filter->applySearch($_GET['search']   ?? '');
$filter->applyType($_GET['type']       ?? '');
$filter->applyMinPrice($_GET['min_price'] ?? '');
$filter->applyMaxPrice($_GET['max_price'] ?? '');
$filter->applyBedrooms($_GET['bedrooms']  ?? '');

$repository  = new PropertyRepository($conn);
$properties  = $repository->findAll($filter);
$view        = new PropertyView();
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

        <div class="row mb-4">
            <div class="col-lg-12">
                <form method="GET" action="properties.php" class="search-form">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Hľadať podľa názvu alebo lokácie..." value="<?php echo $view->val('search'); ?>">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <select name="type" class="form-control">
                                <option value="">Všetky typy</option>
                                <option value="villa"     <?php echo $view->selected('type', 'villa'); ?>>Villa</option>
                                <option value="apartment" <?php echo $view->selected('type', 'apartment'); ?>>Apartmán</option>
                                <option value="penthouse" <?php echo $view->selected('type', 'penthouse'); ?>>Penthouse</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <input type="number" name="min_price" class="form-control" placeholder="Min. cena (€)" value="<?php echo $view->val('min_price'); ?>">
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <input type="number" name="max_price" class="form-control" placeholder="Max. cena (€)" value="<?php echo $view->val('max_price'); ?>">
                        </div>
                        <div class="col-lg-1 col-md-4">
                            <select name="bedrooms" class="form-control">
                                <option value="">Spálne</option>
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($_GET['bedrooms'] ?? '') == $i ? 'selected' : ''; ?>><?php echo $i; ?>+</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-6">
                            <button type="submit" class="btn btn-primary w-100">Hľadať</button>
                        </div>
                        <div class="col-lg-1 col-md-6">
                            <a href="properties.php" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <ul class="properties-filter">
            <li><a class="is_active" href="#!" data-filter="*">Všetky</a></li>
            <li><a href="#!" data-filter=".villa">Villa</a></li>
            <li><a href="#!" data-filter=".apartment">Apartmán</a></li>
            <li><a href="#!" data-filter=".penthouse">Penthouse</a></li>
        </ul>

        <div class="row properties-box">
            <?php if (empty($properties)): ?>
                <?php echo $view->renderEmpty(); ?>
            <?php else: ?>
                <?php foreach ($properties as $property): ?>
                    <?php echo $view->renderCard($property); ?>
                <?php endforeach; ?>
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