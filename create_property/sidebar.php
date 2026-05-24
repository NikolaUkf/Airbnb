<?php
class Sidebar
{
    private string $currentPage;
    private array $menuItems;

    public function __construct()
    {
        $this->currentPage = basename($_SERVER['PHP_SELF']);
        $this->menuItems = [
            [
                'href' => 'admin-dashboard.php',
                'label' => 'Dashboard',
                'icon' => 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z',
            ],
            [
                'href' => 'read.php',
                'label' => 'Inzeráty',
                'icon' => 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z',
            ],
            [
                'href' => 'create.php',
                'label' => 'Nový inzerát',
                'icon' => 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z',
            ],
            [
                'href' => 'view_messages.php',
                'label' => 'Správy',
                'icon' => 'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z',
            ],
            [
                'href' => 'view_reservations.php',
                'label' => 'Rezervácie',
                'icon' => 'M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z',
            ],
        ];
    }

    private function isActive(string $href): bool
    {
        return $this->currentPage === $href;
    }

    private function renderIcon(string $path): string
    {
        return '<svg viewBox="0 0 24 24" fill="currentColor"><path d="' . $path . '"/></svg>';
    }

    private function renderMenuItems(): string
    {
        $html = '';
        foreach ($this->menuItems as $item) {
            $activeClass = $this->isActive($item['href']) ? ' class="active"' : '';
            $html .= '<li>';
            $html .= '<a href="' . $item['href'] . '"' . $activeClass . '>';
            $html .= $this->renderIcon($item['icon']);
            $html .= '<span>' . $item['label'] . '</span>';
            $html .= '</a>';
            $html .= '</li>';
        }
        return $html;
    }

    public function render(): void
    {
        echo '
        <aside class="sidebar">
            <div class="sidebar-logo">
                <a href="admin-dashboard.php">
                    ' . $this->renderIcon('M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z') . '
                    <span>VILLA</span>
                </a>
            </div>
            <ul class="sidebar-menu">
                ' . $this->renderMenuItems() . '
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php">
                    ' . $this->renderIcon('M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z') . '
                    Odhlásiť sa
                </a>
            </div>
        </aside>';
    }
}

(new Sidebar())->render();