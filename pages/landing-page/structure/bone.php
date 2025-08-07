<?php
$WIDPAK_PATH = "./pages/landing-page/";
$STRUCTURE = $WIDPAK_PATH . "structure/";
$WIDGET = $WIDPAK_PATH . "widget/";

$WIDGETS = ["A000-(Simple Intro).html"];

foreach ($WIDGETS as $widget) {
    include $WIDGET . $widget;
}
?>
