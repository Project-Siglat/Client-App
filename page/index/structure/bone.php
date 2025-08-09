<?php
$WIDPAK_PATH = "./page/index/";
$STRUCTURE = $WIDPAK_PATH . "structure/";
$WIDGET = $WIDPAK_PATH . "widget/";

$WIDGETS = [
    "A000-(Simple Intro).html",
    "A001-(Offer).html",
    "A002-(Map).html",
    "A003-(Browser).html",
    "A004-(Contact).html",
    "A005-(Emergency).html",
    "A006-(Footer).html",
];

foreach ($WIDGETS as $widget) {
    include $WIDGET . $widget;
}
?>
