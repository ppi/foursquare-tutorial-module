<?php $view->extend('::base.html.php'); ?>

<?php $view['slots']->start('include_css') ?>
<link rel="stylesheet" href="<?php echo $view['assets']->getUrl('foursquare/css/index.css') ?>"/>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('include_js_body'); ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="<?=$view['assets']->getUrl('foursquare/js/index.js');?>"></script>
<?php $view['slots']->stop(); ?>

<section class='pp-foursquare-tutorial'>
    <h1>Learning how to use the Modules Component on the PPI framework.</h1>
    <div class="map" id="map"></div>
</section>