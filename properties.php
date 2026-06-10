<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty(array_filter($_GET))) {
    $filterKeys = ['search', 'type', 'min_price', 'max_price', 'bedrooms'];
    foreach ($filterKeys as $key) {
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            setcookie('filter_' . $key, $_GET[$key], time() + (86400 * 30), '/');
        } else {
            setcookie('filter_' . $key, '', time() - 3600, '/'); // zmaž ak prázdne
        }
    }
}
if (isset($_GET['reset'])) {
    foreach (['search', 'type', 'min_price', 'max_price', 'bedrooms'] as $key) {
        setcookie('filter_' . $key, '', time() - 3600, '/');
    }
    header('Location: properties.php');
    exit;
}

if (empty(array_filter($_GET))) {
    foreach (['search', 'type', 'min_price', 'max_price', 'bedrooms'] as $key) {
        if (!empty($_COOKIE['filter_' . $key])) {
            $_GET[$key] = $_COOKIE['filter_' . $key];
        }
    }
}
include 'create_property/config.php';

class PropertyFilter
{
    private array $conditions = [];
    private array $params = [];

    public function applyFilters(array $get): void
    {
        if (!empty($get['search'])) {
            $this->conditions[] = "(title LIKE ? OR address LIKE ?)";
            $this->params[] = '%' . $get['search'] . '%';
            $this->params[] = '%' . $get['search'] . '%';
        }

        $mapping = [
            'type' => ['col' => 'type', 'op' => '='],
            'min_price' => ['col' => 'price', 'op' => '>='],
            'max_price' => ['col' => 'price', 'op' => '<='],
            'bedrooms' => ['col' => 'bedrooms', 'op' => '>=']
        ];

        foreach ($mapping as $key => $rule) {
            if (!empty($get[$key])) {
                $this->conditions[] = "{$rule['col']} {$rule['op']} ?";
                $this->params[] = $get[$key];
            }
        }
    }

    public function getWhereClause(): string
    {
        return empty($this->conditions) ? "WHERE 1=1" : "WHERE " . implode(" AND ", $this->conditions);
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
$filter->applyFilters($_GET);

$repository = new PropertyRepository($conn);
$properties = $repository->findAll($filter);
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
    <link rel="stylesheet" href="../asse">

   
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
        <form method="GET" action="properties.php" class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <input type="text" name="search" class="filter-pill" style="min-width:220px;" placeholder="Hľadať podľa názvu alebo lokácie..." value="<?php echo $view->val('search'); ?>">
            <select name="type" class="filter-pill">
                <option value="">Všetky typy</option>
                <option value="villa"     <?php echo $view->selected('type', 'villa'); ?>>Villa</option>
                <option value="apartment" <?php echo $view->selected('type', 'apartment'); ?>>Apartmán</option>
                <option value="penthouse" <?php echo $view->selected('type', 'penthouse'); ?>>Penthouse</option>
            </select>
            <input type="number" name="min_price" class="filter-pill" style="width:140px;" placeholder="Min. cena (€)" value="<?php echo $view->val('min_price'); ?>">
            <input type="number" name="max_price" class="filter-pill" style="width:140px;" placeholder="Max. cena (€)" value="<?php echo $view->val('max_price'); ?>">
            <select name="bedrooms" class="filter-pill" style="width:120px;">
                <option value="">Spálne</option>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($_GET['bedrooms'] ?? '') == $i ? 'selected' : ''; ?>><?php echo $i; ?>+</option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="filter-pill btn-search">Hľadať</button>
            <a href="properties.php?reset=1" class="filter-pill btn-reset" style="line-height:1.2;">Reset</a>
        </form>
    </div>
</div>
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